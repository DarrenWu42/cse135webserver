#!/usr/bin/python3
import cgi
import os

from http import cookies

# set username cookie to destroyed
cookie = cookies.SimpleCookie()
cookie['username'] = "destroyed"

print(cookie)
print("Cache-Control: no-cache")
print("Content-type: text/html\n")
print("<html>")
print("<head>")
print("<title>Python Session Destroyed</title>")
print("</head>")
print("<body>")
print("<h1>Python Session Destroyed</h1>")
print("<hr/>")
print("<a href=\"/hw2/python-cgiform.html\">Back to the Python CGI Form</a><br/>")
print("<a href=\"/cgi-bin/python/python-sessions-1.py\">Session Page 1</a><br />")
print("<a href=\"/cgi-bin/python/python-sessions-2.py\">Session Page 2</a><br/>")
print("</body>")
print("</html>")