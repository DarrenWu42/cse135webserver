#!/usr/bin/python3
import cgi
import cgitb
import os

from datetime import date

cgitb.enable()

print("Cache-Control: no-cache\n")
print("Content-type: text/html\n\n")
print("<html>")
print("<head>")
print("<title>Hello, Python!</title>")
print("</head>")
print("<body>")
print("<h1 align=center'>Hello, Python!</h1>")
print("<hr/>")
print("Hello, World!<br/>")
print(f"This program was generated at: {date.today()}<br/>")
print(f"Your current IP address is: {os.environ['REMOTE_ADDR']}<br/>")
print("</body>")
print("</html>")