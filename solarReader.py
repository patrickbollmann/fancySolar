"""solarReader.py: Reads data from Kostal / Piko solar webinterface"""

__author__      = "Patrick Bollmann"
__email__      = "pbollman@mail.upb.de"

import numpy as np
import solardb
from datetime import datetime
import math
import requests

offset = 0 #german summer time (Use -3600 for german utc+1 winter time)
db = solardb.DataBase()

#get data in textfile
print("getting new data.. (this may take a while)")
r = requests.get('yout piko inverter ip /LogDaten.dat', auth=('user', 'pass'))

with open('readLogDaten.txt', 'w') as fout:
        fout.write(r.text[428:])

#prepare textfile
print("preparing data for parser")
with open('readLogDaten.txt') as fin, open('newReadLogDaten.txt', 'w') as fout:
    for line in fin:
        fout.write(line.replace('\t', '  '))

#prepare to insert data
print("inserting new data")

m = db.query("Select max(timestamp) as max FROM history")   #getting newest timestamp
for x in m:
    maxtime = int(x['max'])
print("latest data in database is: "+str(maxtime))
path = "newReadLogDaten.txt"
content = ""
with open(path,'r') as f:
    content = f.read()
table = []
#adding textfile data in list table
for words in content.split('\n'):
    table.append([s.strip() for s in words.split('  ') if s not in ['', ' ']])

#try to add rows if formatted correctly and timestamp newer than newest timestamp in db
for i in range(len(table)):
    if(len(table[i])> 20 and int(table[i][0])-offset>maxtime):
        try:
            time = int(table[i][0])
            ac1 = int(table[i][18])
            ac2 = int(table[i][22])
            ac3 = int(table[i][26])
            power = ac1+ac2+ac3
            power = power/4 #recalc in kw/h
            timestamp = int(time)
            dt_object = datetime.fromtimestamp(timestamp-offset)
            date = dt_object
            db.query("INSERT INTO history (timestamp, date, wh, ac1, ac2, ac3) VALUES ("+str(timestamp)+", '"+str(date)+"', "+str(power)+", "+str(ac1)+", "+str(ac2)+", "+str(ac3)+");")
            print("row: "+str(i)+" / "+str(len(table))+" added")
        except Exception as e:
            print(e)
    elif(len(table[i])<= 20):
        print("row: "+str(i)+" / "+str(len(table))+" no data found")
    elif(int(table[i][10])<=maxtime):
        print("row: "+str(i)+" / "+str(len(table))+" outdated")
