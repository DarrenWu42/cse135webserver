#!/usr/bin/python3
import cgi
import os

from http import cookies

cookie = cookies.SimpleCookie()

cookie.load(os.environ.get('HTTP_COOKIE')) # turn cookie string from environ to dict
username = cookie['username'].value        # get username value from cookie string

if(username == "" or username == "None"):
    form=cgi.FieldStorage()
    username = form.getvalue('username')
    username = "None" if username == "" else username # if username is still empty, set it to none

print(cookie)    
print("Cache-Control: no-cache")
print("Content-type: text/html\n")
print("<html>")
print("<head>")
print("<title>Python Sessions</title>")
print("</head>")
print("<body>")
print("<h1>Python Sessions Page 1</h1>")
print("<hr/>")
print(f"<b>Name:</b> {username}<br/>")
print("<a href=\"/cgi-bin/python/python-sessions-2.py\">Session Page 2</a><br/>")
print("<a href=\"/hw2/python-cgiform.html\">Python CGI Form</a><br/>")
print("<form style=\"margin-top:30px\" action=\"/cgi-bin/python/python-destroy-session.py\" method=\"get\">")
print("<button type=\"submit\">Destroy Session</button>")
print("</form>")

print("</body>")
print("</html>")