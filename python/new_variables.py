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

