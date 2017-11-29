import os
import glob
import time
import datetime
import requests
import schedule
import json
import urllib2

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
        try:
                context["temperature"] = read_temp()
                print("temp : ", context["temperature"])
        except:
                pass

        #========== Ping Server ==========#
        #need to read all host from postgresqldb      
    	target_url = "https://cryptic-harbor-32168.herokuapp.com/text/server.txt" 
        status = ["stable", "warning", "danger", "error"]
        #in for loop all server hostname
    	ping_res = ping_serv()   
        index = 0 
        for hostname in urllib2.urlopen(target_url):
                hostname = hostname.replace("\xef\xbb\xbf", "")
                data_obj = {"name": "127.0.0.1", "status": "error"}
                data_obj["name"] = hostname
                data_obj["status"] = status[ping_res[index]] 
                context["server"].append(data_obj)    
                index += 1

        url = 'http://192.1.254.77:8989'
        #url = 'https://cryptic-harbor-32168.herokuapp.com/server.php'

        headers = {'Content-type': 'application/json', 'Accept': 'text/plain'}

        res = requests.post(url, data = json.dumps(context), headers = headers)
        print(res)
        print(res.text)

def ping_serv():
        target_url = "https://cryptic-harbor-32168.herokuapp.com/text/server.txt"
    	co_len = 0	
    	for host in urllib2.urlopen(target_url):
    	        co_len += 1	
    	servers = [0] * co_len 
    	c = 0
        for i in range(10):
                for host in urllib2.urlopen(target_url):
                        host = host.replace("\xef\xbb\xbf", "")
                        response = os.system("ping -c 1 " + host)
                        print("ping_res: {}".format(response))
                        if response != 0:
                                print("host: {} is down!".format(host))
                                servers[c] += 1
                        c += 1
                c = 0
                time.sleep(10)

        index = 0
        for server in servers:
                if server == 0:
                        servers[index] = 0
                elif 0 < server < 10:
                        servers[index] = 1
                elif server == 10:
                        servers[index] = 2
                else:
                        servers[index] = 3
                index += 1

        return servers

#schedule.every(1).minutes.do(send_data)

while True:
        #schedule.run_pending()
        send_data()
        print("complete----------------------------") 
        time.sleep(600)
