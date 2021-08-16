#!/usr/bin/python3
import cgi
import os

from datetime import date

print("Cache-Control: no-cache")
print("Content-type: text/html\n")
print("<html>")
print("<head>")
print("<title>General Request Echo</title>")
print("</head>")
print("<body>")
print("<h1 align=center>General Request Echo</h1>")
print("<hr/>")
print(f"<b>Protocol:</b> {os.environ['SERVER_PROTOCOL']}<br/>\n")
print(f"<b>Method:</b> {os.environ['REQUEST_METHOD']}<br/>\n")
print(f"<b>Query String and/or Message Body:</b><br/>\n")
form = cgi.FieldStorage() 
for key in form:
    print(f"<b>{key}</b>: {form.getvalue(key)}<br/>")
print("</body>")
print("</html>")