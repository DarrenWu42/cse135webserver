#!/usr/bin/python3
import cgi
import os

from http import cookies

# load cookie, when reaching here, cookie will always (hopefully) be either None, or entered username
cookie = cookies.SimpleCookie()

cookie_string = os.environ.get('HTTP_COOKIE')
cookie.load(cookie_string)
username = cookie['username'].value

print("Cache-Control: no-cache")
print("Content-type: text/html\n")
print("<html>")
print("<head>")
print("<title>Python Sessions</title>")
print("</head>")
print("<body>")
print("<h1>Python Sessions Page 2</h1>")
print("<hr/>")
print(f"<b>Name:</b> {username}<br/>")
print("<a href=\"/cgi-bin/python/python-sessions-1.py\">Session Page 1</a><br/>")
print("<a href=\"/hw2/python-cgiform.html\">Python CGI Form</a><br/>")
print("<form style=\"margin-top:30px\" action=\"/cgi-bin/python/python-destroy-session.py\" method=\"get\">")
print("<button type=\"submit\">Destroy Session</button>")
print("</form>")

print("</body>")
print("</html>")