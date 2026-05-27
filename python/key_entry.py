import psycopg2
import smtplib
from email.mime.multipart import MIMEMultipart
from datetime import datetime, timedelta
from email.mime.text import MIMEText
import pandas as pd
import numpy as np
import base64
from xml.dom import minidom
from ftplib import FTP
import os
import time

xmldoc = minidom.parse('config.xml')
interrupt_item = xmldoc.getElementsByTagName('interruptprocess') 
processinterrupt = interrupt_item[0].firstChild.nodeValue

class mailer:
    def __init__(self):
        itemlist = xmldoc.getElementsByTagName('emailsrv') 
        self.mailsrv = itemlist[0].firstChild.nodeValue
        itemlist = xmldoc.getElementsByTagName('emailport') 
        self.mailport = itemlist[0].firstChild.nodeValue
        
    def sendmail(self, msg, sender, receiver):
        server = smtplib.SMTP(self.mailsrv, self.mailport)
        server.sendmail(sender, receiver, msg)
        server.quit()

class DBHelper:
    def __init__(self):
        itemlist = xmldoc.getElementsByTagName('keyentrysrv') 
        self.host = itemlist[0].firstChild.nodeValue
        itemlist = xmldoc.getElementsByTagName('keyentryuser') 
        self.user = itemlist[0].firstChild.nodeValue
        itemlist = xmldoc.getElementsByTagName('keyentrypass') 
        passw = base64.b64decode(itemlist[0].firstChild.nodeValue)
#        passw = str(passw, "utf-8")
        self.password = passw
        itemlist = xmldoc.getElementsByTagName('keyentrydb') 
        self.db = itemlist[0].firstChild.nodeValue
        self.port = 5432

    def __connect__(self):
        self.con = psycopg2.connect(host=self.host, user=self.user, password=self.password, database=self.db, port=self.port)
        self.cur = self.con.cursor()

    def __disconnect__(self):
        self.con.close()

    def fetch(self, sql):
        self.__connect__()
        self.cur.execute(sql)
        result = self.cur.fetchall()
        self.__disconnect__()
        return result

    def execute(self, sql, val):
        self.__connect__()
        self.cur.execute(sql, val)
        self.con.commit()
        self.__disconnect__()
    
    def update(self, sql, val):
        self.__connect__()
        self.cur.execute(sql, val)
        self.__disconnect__()
        
    def check_null(self, data):
        if data is None or data == 999 or data == 'NaN':
            return np.nan
        else:
            return data

class CLIHelper:
    def __init__(self):
        itemlist = xmldoc.getElementsByTagName('climatesrv') 
        self.climatesrv = itemlist[0].firstChild.nodeValue
        itemlist = xmldoc.getElementsByTagName('climatedb') 
        self.climatedb = itemlist[0].firstChild.nodeValue
        itemlist = xmldoc.getElementsByTagName('climateuser') 
        self.climateuser = itemlist[0].firstChild.nodeValue
        itemlist = xmldoc.getElementsByTagName('climatepass') 
        cpass = base64.b64decode(itemlist[0].firstChild.nodeValue)
#       cpass = str(cpass, "utf-8")
        self.climatepass = cpass
        itemlist = xmldoc.getElementsByTagName('climateftpuser') 
        self.climateftpuser = itemlist[0].firstChild.nodeValue
        itemlist = xmldoc.getElementsByTagName('climateftppass') 
        passw = base64.b64decode(itemlist[0].firstChild.nodeValue)
#        passw = str(passw, "utf-8")
        self.climateftppass = passw
        self.climateftpport = 21
        self.port = 5432
        
    def __connect__(self):
        self.con = psycopg2.connect(host=self.climatesrv, user=self.climateuser, password=self.climatepass, database=self.climatedb, port=self.port)
        self.cur = self.con.cursor()

    def __disconnect__(self):
        self.con.close()

    def fetch(self, sql, val):
        self.__connect__()
        self.cur.execute(sql,val)
        result = self.cur.fetchone()
        self.__disconnect__()
        return result
    
    def execute(self, sql, val):
        self.__connect__()
        self.cur.execute(sql, val)
        self.con.commit()
        self.__disconnect__()
    
    def file_sender(self,file_name):
        HOST = self.climatesrv
        PORT = self.climateftpport
        ftp = FTP()
        ftp.connect(HOST, PORT)
        ftp.login(user=self.climateftpuser, passwd = self.climateftppass)
        fp = open(file_name, 'rb')
        ftp.storbinary('STOR %s' % os.path.basename(file_name), fp, 1024)
        ftp.quit()

####
DBAccess = DBHelper()
fields = "station_no, date_entry, rainfall, dry_bulb_temperature, wet_bulb_temperature, max_temperature, min_temperature, sunshine_hours, radiation, evaporation, rh, dew, id, logged_value"
table = "obs_data"
conditions = "is_processed = 'N'"
#upsql = (f"SELECT {fields} " f"FROM {table} " f"WHERE {conditions};")
upsql = "SELECT %s FROM %s WHERE %s" % (fields,table,conditions)
unprocessed_records = DBAccess.fetch(upsql)
daily_cols = ['station_no','lsd', 'rain_24h_period', 'rain_24h_type','rain_24h_count','rain_24h','rain_24h_raw','max_air_temp','max_air_temp_period','max_air_temp_raw',\
              'sunshine_duration','sunshine_duration_raw',\
              'radiation','radiation_raw', 'evaporation', 'evaporation_period', 'evaporation_raw']
daily_min_col = ['station_no','lsd', 'min_air_temp', 'min_air_temp_period', 'min_air_temp_raw']
sub_daily_cols = ['station_no','lsd', 'air_temp','air_temp_raw', 'wet_bulb','wet_bulb_raw', 'dew_point', 'dew_point_raw', 'rel_humidity', 'rel_humidity_raw', 'rain_3h_hours']
dfdaily_ = pd.DataFrame(columns=daily_cols)
dfdailymin_ = pd.DataFrame(columns=daily_min_col)
dfsubdaily_ = pd.DataFrame(columns=sub_daily_cols)

for x in unprocessed_records:
    print("...Processing Records")
    evap_period = 1
    max_temp_period = 1
    rain_count = 1
    rain_period = 1
    rain_type = "'"+'rain'+"'"
    min_temp_period = 1
    
    station_no = x[0]
    date = x[1]
    #RAIN
    rainfall = x[2]
    if rainfall is None or rainfall == 999 or rainfall == 'NaN':
        rain_count = np.nan
        rain_period = np.nan
        rainfall = np.nan
        rain_type = "'"+'missing'+"'"
    elif rainfall == 0:
        rain_count = 0
        rain_period = 1
        rain_type = np.nan
        
    dry_bulb = DBAccess.check_null(x[3])
    wet_bulb = DBAccess.check_null(x[4])
    
    #MAX_TEMP
    max_temp = x[5]
    if max_temp is None or max_temp == 999 or max_temp == 'NaN':
        max_temp_period = np.nan
        max_temp = np.nan
    
    #MIN_TEMP
    min_temp = x[6]
    if min_temp is None or min_temp == 999 or min_temp == 'NaN':
        min_temp_period = np.nan
        min_temp = np.nan
    
    #SUN
    sun_hrs = DBAccess.check_null(x[7])
    #RAD
    rad = DBAccess.check_null(x[8])
    
    #EVAPORATION
    evap = x[9]
    if evap is None or evap == 999 or evap == 'NaN':
        evap_period = np.nan
        evap = np.nan
    
    #RH
    rh = DBAccess.check_null(x[10])
    #DEW
    dew = DBAccess.check_null(x[11])
    #ID
    record_id = x[12]
    
    daily_date = date - timedelta(days=1)
    
    ##MINS
    dfdailymin_ = dfdailymin_.append({'station_no': station_no, 'lsd': date.strftime("%Y-%m-%d"), 'min_air_temp' : min_temp, 'min_air_temp_period' : min_temp_period,\
                                      'min_air_temp_raw' : min_temp}, ignore_index=True)
    ##SUBS
    dfsubdaily_ = dfsubdaily_.append({'station_no': station_no, 'lsd': date, 'air_temp' : dry_bulb, 'air_temp_raw' : dry_bulb, 'wet_bulb' : wet_bulb,\
                                            'wet_bulb_raw' : wet_bulb, 'dew_point' : dew, 'dew_point_raw' : dew, 'rel_humidity' : rh, \
                                            'rel_humidity_raw' : rh, 'rain_3h_hours' : np.nan}, ignore_index=True)
    
    #DAILIES
    dfdaily_ = dfdaily_.append({'station_no': station_no, 'lsd': daily_date.strftime("%Y-%m-%d"), 'rain_24h_period' : rain_period,\
                                'rain_24h_type' : rain_type, 'rain_24h_count' : rain_count, 'rain_24h' : rainfall,'rain_24h_raw' : rainfall, 'max_air_temp' : max_temp,\
                                'max_air_temp_period' : max_temp_period, 'max_air_temp_raw' : max_temp, 'sunshine_duration' : sun_hrs,'sunshine_duration_raw' : sun_hrs,
                                'radiation' : rad,'radiation_raw' : rad, 'evaporation' : evap,\
                                'evaporation_period':evap_period, 'evaporation_raw' : evap}, ignore_index=True)
    #UPDATES
    fields_ud = "is_processed = 'Y'"
    conditions = "id = %s"
    val_up = (record_id,)
    #upsql = (f"update {table} " f"set {fields_ud} " f"where {conditions}")
    upsql = "update %s set %s WHERE %s" % (table,fields_ud,conditions)
    DBAccess.execute(upsql,val_up)


if unprocessed_records:
    now = datetime.now()
    dt_now = now.strftime("%y%m%d%H%S")
    FTPSrv = CLIHelper()
    
    #DAILY
    headname = pd.DataFrame([['obs_daily']])
    headname.to_csv('obs_daily'+dt_now+'.csv', index=False, header=False, mode='a')
    dfdaily_.to_csv('obs_daily'+dt_now+'.csv', index=False, mode='a')
    
    #DAILY MIN
    headname = pd.DataFrame([['obs_daily']])
    headname.to_csv('obs_dailymin'+dt_now+'.csv', index=False, header=False, mode='a')
    dfdailymin_.to_csv('obs_dailymin'+dt_now+'.csv', index=False, mode='a')
    
    #SUB DAILY
    headname = pd.DataFrame([['obs_subdaily']])
    headname.to_csv('obs_subdaily'+dt_now+'.csv', index=False, header=False, mode='a')
    dfsubdaily_.to_csv('obs_subdaily'+dt_now+'.csv', index=False, mode='a')
    
    ##SEND FILES
    
    FTPSrv.file_sender('obs_daily'+dt_now+'.csv')
    FTPSrv.file_sender('obs_dailymin'+dt_now+'.csv')
    FTPSrv.file_sender('obs_subdaily'+dt_now+'.csv')
    
    print("FTP Successful")
    time.sleep(int(processinterrupt))
    ##UPDATE CLIMATE DB
    for x in unprocessed_records:
        station_no = x[0]
        date = x[1]
        logged_value = x[13]
        record_id = x[12]
        daily_date = date - timedelta(days=1)
        subdailydate = date
        dailymindate = date.strftime("%Y-%m-%d")
        dailymindate = dailymindate+" 00:00:00"
        dailydt = daily_date.strftime("%Y-%m-%d")
        daily_datee = dailydt+" 00:00:00"
        print(daily_datee)
        
        #update subdaily
        fieldsid = "id"
        table = "obs_subdaily"
        conditions_c = "station_no = %s and lsd = %s"
        setter = "change_user = %s"
        set_id = "id = %s"
        #climate_sql = (f"SELECT {fieldsid} " f"FROM {table} " f"WHERE {conditions_c};")
        climate_sql = "SELECT %s FROM %s WHERE %s" % (fieldsid,table,conditions_c)
        val =(station_no, subdailydate)
        subdaily_records = FTPSrv.fetch(climate_sql, val)
        #cli_up_sql = (f"UPDATE {table} " f"SET {setter} " f"WHERE {set_id};")
        cli_up_sql = "UPDATE %s SET %s WHERE %s" % (table,setter,set_id)
        cli_up_val = (logged_value, subdaily_records[0])
        FTPSrv.execute(cli_up_sql, cli_up_val)
        print('***Updated Subdaily records')
        
        #update daily min
        daily_tbl = "obs_daily"
        #daily_min_sql = (f"SELECT {fieldsid} " f"FROM {daily_tbl} " f"WHERE {conditions_c};")
        daily_min_sql = "SELECT %s FROM %s WHERE %s" % (fieldsid,daily_tbl,conditions_c)
        val_min =(station_no, dailymindate)
        mindaily_records = FTPSrv.fetch(daily_min_sql, val_min)
        #cli_up_sql = (f"UPDATE {daily_tbl} " f"SET {setter} " f"WHERE {set_id};")
        cli_up_sql = "UPDATE %s SET %s WHERE %s" % (daily_tbl,setter,set_id)
        cli_up_val = (logged_value, mindaily_records[0])
        FTPSrv.execute(cli_up_sql, cli_up_val)
        print("***Updated Daily min records")
        
        #update daily
        #daily_min_sql = (f"SELECT {fieldsid} " f"FROM {daily_tbl} " f"WHERE {conditions_c};")
        daily_min_sql = "SELECT %s FROM %s WHERE %s" % (fieldsid,daily_tbl,conditions_c)
        val_min =(station_no, daily_datee)
        daily_records = FTPSrv.fetch(daily_min_sql, val_min)
        #cli_up_sql = (f"UPDATE {daily_tbl} " f"SET {setter} " f"WHERE {set_id};")
        cli_up_sql = "UPDATE %s SET %s WHERE %s" % (daily_tbl,setter,set_id)
        cli_up_val = (logged_value, daily_records[0])
        FTPSrv.execute(cli_up_sql, cli_up_val)
        print("***Updated Daily records")
        print("-----------------------------------")
        
        fields_ud = "is_user_processed = 'Y'"
        table = "obs_data"
        conditions = "id = %s"
        val_up = (record_id,)
        #upsql = (f"update {table} " f"set {fields_ud} " f"where {conditions}")
        upsql = "UPDATE %s SET %s WHERE %s" % (table,fields_ud,conditions)
        DBAccess.execute(upsql,val_up)
        
        
    ##END_UPDATES
else:
    print("-----------------------------------")
    print("No unprocessed Records Found.")
    print("-----------------------------------")