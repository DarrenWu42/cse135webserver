#!/usr/bin/python3
import cgi
import os

from http import cookies

cookie = cookies.SimpleCookie()

form=cgi.FieldStorage()
username = form.getvalue('username')

if(username != ""): # if username from form is not empty
    cookie['username'] = username # set cookie to username
    print(cookie) # print header to set cookie
else: # else
    cookie_string = os.environ.get('HTTP_COOKIE') # load cookie string from environ
    cookie.load(cookie_string) # turn cookie string to dict
    username = cookie['username'].value # get username value from cookie string
    username = "None" if username == "" else username # if username is still empty, set it to none

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