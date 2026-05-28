import psycopg2

# Hardcoded DB config for user 'anuj'
host = '192.168.7.18'
user = 'anuj'
password = 'Simple10'
db = 'manualDB'
port = 5432

try:
    con = psycopg2.connect(host=host, user=user, password=password, database=db, port=port)
    cur = con.cursor()
    cur.execute('SELECT station_no FROM stations')
    rows = cur.fetchall()
    station_nos = [str(row[0]) for row in rows]
    print(','.join(station_nos))
    con.close()
except Exception as e:
    print('Error:', e)
