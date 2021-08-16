#!/usr/bin/python3
import json
import os

from datetime import datetime

print("Cache-Control: no-cache")
print("Content-type: application/json\n")

now = datetime.now()
json_send = {"message":"Hello, Python!", "date":now.strftime('%a %b %d %H:%M:%S %Y'), "currentIP":os.environ['REMOTE_ADDR']}

print(json.dumps(json_send))