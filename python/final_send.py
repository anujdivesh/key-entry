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
        # Hardcoded DB config for user 'anuj'
        self.host = '192.168.7.18'
        self.user = 'anuj'
        self.password = 'Simple10'
        self.db = 'manualDB'
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
        ftp.login(user='anuj_ftp', passwd = 'clide1')
        fp = open(file_name, 'rb')
        ftp.storbinary('STOR %s' % os.path.basename(file_name), fp, 1024)
        ftp.quit()

####
DBAccess = DBHelper()

fields = [
    "station_no", "date_entry", "wind_dir", "wind_speed_bft", "visibility_code", "gale_flag", "lightning_flag", "thunder_flag", "fog_flag", "dew_flag", "ground_temp", "msl_pres", "pres_weather_bft", "soil_temp_5cm", "soil_temp_10cm", "soil_temp_20cm", "soil_temp_30cm", "soil_temp_50cm", "soil_temp_100cm"
]
table = "obs_data"
conditions = "variables_flag = 'N'"
upsql = f"SELECT {', '.join(fields)} FROM {table} WHERE {conditions};"
unprocessed_records = DBAccess.fetch(upsql)

# Create DataFrame
df = pd.DataFrame(unprocessed_records, columns=fields)
# Multiply wind_dir by 10 if present
if 'wind_dir' in df.columns:
    df['wind_dir'] = df['wind_dir'] * 10

# If wind_dir is 0, set wind_speed_bft to 0
if 'wind_speed_bft' in df.columns and 'wind_dir' in df.columns:
    df.loc[df['wind_dir'] == 0, 'wind_speed_bft'] = 0
# Rename 'date_entry' to 'lsd'
df = df.rename(columns={"date_entry": "lsd"})

# ---- Split into obs_daily and obs_subdaily ----
daily_cols = [
    "station_no",
    "lsd",
    "gale_flag",
    "lightning_flag",
    "thunder_flag",
    "fog_flag",
    "dew_flag",
    "ground_temp",
]

obs_daily = df[daily_cols].copy()
# Remove columns where all values are 'N' for the flag columns
flag_cols = ["gale_flag", "lightning_flag", "thunder_flag", "fog_flag", "dew_flag"]
for col in flag_cols:
    if col in obs_daily.columns:
        if (obs_daily[col] == "N").all():
            obs_daily = obs_daily.drop(columns=[col])
        # else: do nothing, keep 'Y' as value
        # Keep only 'Y' values, drop if not 'Y'
# Remove columns where all values are 999
for col in obs_daily.columns:
    if (obs_daily[col] == 999).all():
        obs_daily = obs_daily.drop(columns=[col])
# Format lsd to only year, month, day
if "lsd" in obs_daily.columns:
    obs_daily["lsd"] = pd.to_datetime(obs_daily["lsd"]).dt.strftime("%Y-%m-%d")

subdaily_id_cols = [
    "station_no",
    "lsd",
    "visibility_code",
    "wind_dir",
    "wind_speed_bft",
    "msl_pres",
    "pres_weather_bft",
]
soil_cols = [
    "soil_temp_5cm",
    "soil_temp_10cm",
    "soil_temp_20cm",
    "soil_temp_30cm",
    "soil_temp_50cm",
    "soil_temp_100cm",
]
soil_depth_map = {
    "soil_temp_5cm": 5,
    "soil_temp_10cm": 10,
    "soil_temp_20cm": 20,
    "soil_temp_30cm": 30,
    "soil_temp_50cm": 50,
    "soil_temp_100cm": 100,
}




# Output obs_subdaily as one row per station/date, with columns: id cols, soil_depth, soil_temp, soil_depth, soil_temp, ... (headers repeated, no suffixes)
soil_depths = [5, 10, 20, 30, 50, 100]
soil_temp_col_map = {
    5: "soil_temp_5cm",
    10: "soil_temp_10cm",
    20: "soil_temp_20cm",
    30: "soil_temp_30cm",
    50: "soil_temp_50cm",
    100: "soil_temp_100cm",
}

obs_subdaily_rows = []
for idx, row in df.iterrows():
    base = [row[c] for c in subdaily_id_cols]
    soil_parts = []
    for d in soil_depths:
        soil_parts.extend([d, row[soil_temp_col_map[d]]])
    obs_subdaily_rows.append(base + soil_parts)

# Build columns: id cols, then soil_depth, soil_temp, soil_depth, soil_temp, ...
obs_subdaily_columns = subdaily_id_cols + [col for d in soil_depths for col in ("soil_depth", "soil_temp")]
obs_subdaily_expanded = pd.DataFrame(obs_subdaily_rows, columns=obs_subdaily_columns)


def _with_title_and_header_rows(dataframe: pd.DataFrame, title: str) -> pd.DataFrame:
    # Only return the header and data rows; title row will be handled separately when saving
    header_row = pd.DataFrame([list(dataframe.columns)], columns=dataframe.columns)
    out = pd.concat([header_row, dataframe], ignore_index=True)
    return out


obs_daily_out = _with_title_and_header_rows(obs_daily, "obs_daily")

import pandas.api.types as pdt
# Remove columns from obs_subdaily where all values are 999 (only for numeric columns)
for col in obs_subdaily_expanded.columns:
    if pdt.is_numeric_dtype(obs_subdaily_expanded[col]):
        if (obs_subdaily_expanded[col] == 999).all():
            obs_subdaily_expanded = obs_subdaily_expanded.drop(columns=[col])

# Remove all columns named exactly 'soil_depth' or 'soil_temp' from obs_subdaily
cols_to_remove = [col for col in obs_subdaily_expanded.columns if col == 'soil_depth' or col == 'soil_temp']
obs_subdaily_no_soil = obs_subdaily_expanded.drop(columns=cols_to_remove)
obs_subdaily_out = _with_title_and_header_rows(obs_subdaily_no_soil, "obs_subdaily")


# Write the title row as a single cell, then append the rest, with date in filename
dt_now = datetime.now().strftime("%Y%m%d")
obs_daily_filename = f"obs_daily{dt_now}.csv"
obs_subdaily_filename = f"obs_subdaily{dt_now}.csv"
with open(obs_daily_filename, "w") as f:
    f.write("obs_daily\n")
    obs_daily_out.to_csv(f, index=False, header=False)
with open(obs_subdaily_filename, "w") as f:
    f.write("obs_subdaily\n")
    obs_subdaily_out.to_csv(f, index=False, header=False)

# Only print CSVs that have more than just station_no and lsd columns
def has_extra_columns(df):
    base_cols = {"station_no", "lsd"}
    return len([c for c in df.columns if c not in base_cols]) > 0

FTPSrv = CLIHelper()
if has_extra_columns(obs_daily):
    print("obs_daily preview:")
    print(obs_daily.head())
    FTPSrv.file_sender(obs_daily_filename)
else:
    print(f"{obs_daily_filename} will NOT be sent (only station_no, lsd)")

if has_extra_columns(obs_subdaily_expanded):
    print("obs_subdaily preview:")
    print(obs_subdaily_expanded.head())
    FTPSrv.file_sender(obs_subdaily_filename)
else:
    print(f"{obs_subdaily_filename} will NOT be sent (only station_no, lsd)")





# Output obs_subdaily_soil_temps as station_no, lsd, soil_depth, soil_temp, soil_depth, soil_temp, ... (one pair per depth, no repetition)
soil_depths = [5, 10, 20, 30, 50, 100]
cols = ['station_no', 'lsd'] + [item for d in soil_depths for item in (f'soil_depth_{d}', f'soil_temp_{d}')]
rows = []
for _, row in df.iterrows():
    base = [row['station_no'], row['lsd']]
    pairs = []
    for d in soil_depths:
        pairs.extend([d, row[soil_temp_col_map[d]]])
    rows.append(base + pairs)
header = ['station_no', 'lsd'] + [item for d in soil_depths for item in ('soil_depth', 'soil_temp')]
obs_subdaily_soil_temps = pd.DataFrame(rows, columns=cols)
"""
# Write obs_subdaily_soil_temps.csv
obs_subdaily_soil_temps_filename = f"obs_subdaily_soil_temps{dt_now}.csv"
with open(obs_subdaily_soil_temps_filename, "w") as f:
    f.write("obs_subdaily_soil_temps\n")
    pd.DataFrame([header], columns=cols).to_csv(f, index=False, header=False)
    obs_subdaily_soil_temps.to_csv(f, index=False, header=False)

# Send obs_subdaily_soil_temps.csv via FTP if it has extra columns
if has_extra_columns(obs_subdaily_soil_temps):
    print("obs_subdaily_soil_temps preview:")
    print(obs_subdaily_soil_temps.head())
    FTPSrv.file_sender(obs_subdaily_soil_temps_filename)
else:
    print(f"{obs_subdaily_soil_temps_filename} will NOT be sent (only station_no, lsd)")
"""
# TEST: Write a CSV with only one pair of soil_depth,soil_temp for the first depth


# Loop through all depths and create a CSV for each
csv_files_to_remove = [obs_daily_filename, obs_subdaily_filename]
for test_soil_depth in soil_depths:
    test_cols = ['station_no', 'lsd', f'soil_depth_{test_soil_depth}', f'soil_temp_{test_soil_depth}']
    test_header = ['station_no', 'lsd', 'soil_depth', 'soil_temp']
    single_depth_df = obs_subdaily_soil_temps[test_cols].copy()
    # Skip CSV creation if all soil_temp values for this depth are 999
    soil_temp_col = f'soil_temp_{test_soil_depth}'
    if single_depth_df[soil_temp_col].eq(999).all():
        print(f"Skipping {soil_temp_col} (all values 999)")
        continue
    filename = f"obs_subdaily_soil_temps_{test_soil_depth}cm_{dt_now}.csv"
    with open(filename, "w") as f:
        f.write("obs_subdaily_soil_temps\n")
        pd.DataFrame([test_header], columns=test_cols).to_csv(f, index=False, header=False)
        single_depth_df.to_csv(f, index=False, header=False)
    csv_files_to_remove.append(filename)
    # Show preview for each CSV
    if has_extra_columns(single_depth_df):
        print(f"obs_subdaily_soil_temps ({test_soil_depth}cm) preview:")
        print(single_depth_df.head())
        FTPSrv.file_sender(filename)
    else:
        print(f"{filename} will NOT be sent (only station_no, lsd)")

# Remove all generated CSV files after sending
for csv_file in csv_files_to_remove:
    try:
        os.remove(csv_file)
        print(f"Removed {csv_file}")
    except Exception as e:
        print(f"Could not remove {csv_file}: {e}")

if not df.empty:
    # Get unique dates sent (from 'lsd' column, which is formatted as YYYY-MM-DD)
    sent_dates = df['lsd'].unique()
    for sent_date in sent_dates:
        # Convert numpy.datetime64 to string if needed
        if hasattr(sent_date, 'astype'):
            sent_date_str = str(sent_date.astype('M8[D]'))
        else:
            sent_date_str = str(sent_date)
        # Update obs_daily table for this date
        #send_date_str = sent_date_str + " 09:00:00"
        update_sql = "UPDATE obs_data SET variables_flag = 'Y' WHERE DATE(date_entry) = %s"
        DBAccess.execute(update_sql, (sent_date_str,))
        print(f"Updated obs_daily for date_entry={sent_date_str}, set variables_flag='Y'")

