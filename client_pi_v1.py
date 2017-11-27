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

        current_time = datetime.datetime.now()
        #context = {"timestamp": current_time, "data_list": []}

        location = "ITSD Room"
        context = {"location": location, "temperature": 1000, "server": []}

        #========== Read Temp ==========#
        data_obj1 = {"name": "temperature", "res": True, "data": 0.0}
        try:
                data_obj1["data"] = read_temp()
                print("temp: ", data_obj1["data"])
        except:
                data_obj1["res"] = False
                pass

        #context["data_list"].append(data_obj1)
        context["temperature"] = data_obj1["data"]

        #========== Ping Server ==========#
        #data_obj2 = {"name": "192.1.100.13", "res": True, "data": True}
        data_obj2 = {"name": "192.1.100.13", "status": True}

        hostname = "192.1.100.13"
        response = os.system("ping -c 1 " + hostname)
        print("ping_res: {}".format(response))
        if response != 0:
                #data_obj2["data"] = False
                data_obj2["status"] = False
                print("host: {} is down!".format(hostname))

        #context["data_list"].append(data_obj2)
        context["server"].append(data_obj2)

        #data_obj3 = {"name": "192.1.100.14", "res": True, "data": True}
        data_obj3 = {"name": "192.1.100.14", "status": True}

        hostname = "192.1.100.14"
        response = os.system("ping -c 1 " + hostname)
        print("ping_res: {}".format(response))
        if response != 0:
                #data_obj3["data"] = False
                data_obj3["status"] = False
                print("host: {} is down!".format(hostname))

def send_data():

        current_time = datetime.datetime.now()
        #context = {"timestamp": current_time, "data_list": []}

        location = "ITSD Room"
        context = {"location": location, "temperature": 1000, "server": []}

        #========== Read Temp ==========#
        data_obj1 = {"name": "temperature", "res": True, "data": 0.0}
        try:
                data_obj1["data"] = read_temp()
                print("temp: ", data_obj1["data"])
        except:
                data_obj1["res"] = False
                pass

        #context["data_list"].append(data_obj1)
        context["temperature"] = data_obj1["data"]

        #========== Ping Server ==========#
        #data_obj2 = {"name": "192.1.100.13", "res": True, "data": True}
        data_obj2 = {"name": "192.1.100.13", "status": True}

        hostname = "192.1.100.13"
        response = os.system("ping -c 1 " + hostname)
        print("ping_res: {}".format(response))
        if response != 0:
                #data_obj2["data"] = False
                data_obj2["status"] = False
                print("host: {} is down!".format(hostname))

        #context["data_list"].append(data_obj2)
        context["server"].append(data_obj2)

        #data_obj3 = {"name": "192.1.100.14", "res": True, "data": True}
        data_obj3 = {"name": "192.1.100.14", "status": True}

        hostname = "192.1.100.14"
        response = os.system("ping -c 1 " + hostname)
        print("ping_res: {}".format(response))
        if response != 0:
                #data_obj3["data"] = False
                data_obj3["status"] = False
                print("host: {} is down!".format(hostname))

        #context["data_list"].append(data_obj3)
        context["server"].append(data_obj3)

        #url = 'http://192.1.254.127:8989'
        #url = 'http://192.1.254.77:8989'
        url = 'https://cryptic-harbor-32168.herokuapp.com/server.php'

        testdata = {"temperature": data_obj1["data"], "server":[{"name":"192.1.100.13", "status":"True"}, {"name":"192.1.100.14", "status":"True"}]}

        headers = {'Content-type': 'application/json', 'Accept': 'text/plain'}

        res = requests.post(url, data = json.dumps(context), headers = headers)
        print(res)
        print(res.text)

schedule.every(1).minutes.do(send_data)

while True:
        schedule.run_pending()
        #send_data()
        time.sleep(1)