#!/usr/bin/python3
import os

from datetime import datetime

print("Cache-Control: no-cache")
print("Content-type: text/html\n")
print("<html>")
print("<head>")
print("<title>Hello, Python!</title>")
print("</head>")
print("<body>")
print("<h1 align=center>Hello, Python!</h1>")
print("<hr/>")
print("Hello, World!<br/>")

now = datetime.now()
print(f"This program was generated at: {now.strftime('%a %b %d %H:%M:%S %Y')}<br/>")

print(f"Your current IP address is: {os.environ['REMOTE_ADDR']}<br/>")
print("</body>")
print("</html>")