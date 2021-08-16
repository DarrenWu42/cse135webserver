#!/usr/bin/python3
import cgi
import os

from datetime import date

print("Cache-Control: no-cache")
print("Content-type: text/html\n")
print("<html>")
print("<head>")
print("<title>POST Request Echo</title>")
print("</head>")
print("<body>")
print("<h1 align=center>POST Request Echo</h1>")
print("<hr/>")
print("<b>Message Body:</b><br/>")

form = cgi.FieldStorage() 
for key in form:
    print(f"<b>{key}</b>: {form.getvalue(key)}<br/>")
    
print("</body>")
print("</html>")