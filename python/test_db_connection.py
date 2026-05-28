import psycopg2
from xml.dom import minidom


# Hardcoded DB config for testing
host = '192.168.7.18'
user = 'anuj'
password = 'Simple10'
db = 'manualDB'
port = 5432

try:
    con = psycopg2.connect(host=host, user=user, password=password, database=db, port=port)
    print('Connection successful!')
    con.close()
except Exception as e:
    print('Connection failed:')
    print(e)
