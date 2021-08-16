#!/usr/bin/python3
import json
import os

from datetime import date

print("Cache-Control: no-cache")
print("Content-type: application/json\n")

json_send = {'message':'Hello, Python!', 'date':date.today(), 'cuurentIP':os.environ['REMOTE_ADDR']}

print(json.dumps(json_send))