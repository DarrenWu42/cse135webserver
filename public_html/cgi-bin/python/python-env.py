#!/usr/bin/python3
import os

print("Cache-Control: no-cache")
print("Content-type: text/html\n")
print("<html>")
print("<head>")
print("<title>Environment Variables</title>")
print("</head>")
print("<body>")
print("<h1 align=center>Environment Variables</h1>")
print("<hr/>")

for param in os.environ.keys():
   print(f"<b>{param}</b>: {os.environ[param]}<\br>")

print("</body>")
print("</html>")