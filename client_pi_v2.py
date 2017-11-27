import os
import glob
import time
import datetime
import requests
import schedule
import json

os.system('sudo modprobe w1-gpio')
os.system('sudo modprobe w1-therm')

base_dir = '/sys/bus/w1/devices/'
device_folder = glob.glob(base_dir + '28*')[0]
device_file = device_folder + '/w1_slave'

def read_temp_raw():
        f = open(device_file, 'r')
        lines = f.readlines()
        f.close()
        return lines

def read_temp():
        lines = read_temp_raw()
        while lines[0].strip()[-3:] != 'YES':
                time.sleep(0.2)
                lines = read_temp_raw()
        equals_pos = lines[1].find('t=')
        if equals_pos != -1:
                temp_string = lines[1][equals_pos+2:]
                temp_c = float(temp_string) / 1000.0
                return temp_c

def send_data():

        location = "ITSD Room" #"Server Room"
        context = {"location": location, "temperature": 1000, "server": []}

        #========== Read Temp ==========#
        data_obj1 = {"name": "temperature", "res": True, "data": 0.0}
        try:
                context["temperature"] = read_temp()
                print("temp: ", context["temperature"])
        except:
                pass

        #========== Ping Server ==========#
        #need to read all host from postgresqldb
        
        status = ["stable", "warning", "danger", "error"]
        #in for loop all server hostname
        #for hostname in cur.fetchall():
                data_obj = {"name": "127.0.0.1", "status": "error"}
                #data_obj["name"] = hostname
                #data_obj["status"] = status[ping_serv(hostname)] 
                context["server"].append(data_obj)

        url = 'https://cryptic-harbor-32168.herokuapp.com/server.php'

        headers = {'Content-type': 'application/json', 'Accept': 'text/plain'}

        res = requests.post(url, data = json.dumps(context), headers = headers)
        print(res)
        print(res.text)

def ping_serv(hostname):
        count = 0
        for i in range(10):
                response = os.system("ping -c 1 " + hostname)
                print("ping_res: {}".format(response))
                if response != 0:
                        print("host: {} is down!".format(hostname))
                        count += 1;
                time.sleep(1)
        if count == 0:
                return 0
        else if 0 < count < 10:
                return 1
        else if count == 10:
                return 2
        else: 
                return 3

#schedule.every(1).minutes.do(send_data)

while True:
        schedule.run_pending()
        send_data()
        time.sleep(1)