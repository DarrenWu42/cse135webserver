package main

import (
	"fmt"
	"net/http"
	"net/http/cgi"
)

func main() {
	cgi.Serve(http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		header := w.Header()

		r.ParseForm()
		post := r.PostForm
		username := post.Get("username")

		if username == "" { // if the value from form is empty
			cookie, _ := r.Cookie("username") // get cookie from request
			username = cookie.Value           // get cookie value from cookie
		} else { // if form had something
			// source: https://astaxie.gitbooks.io/build-web-application-with-golang/content/en/06.1.html
			cookie := http.Cookie{Name: "username", Value: username} // create cookie
			http.SetCookie(w, &cookie)                               // set cookie to response
		}

		header.Set("Cache-Control", "no-cache")
		header.Set("Content-Type", "text/html")

		fmt.Fprintf(w, "<html><head><title>Go Sessions</title></head><body><h1>Go Page 1</h1><hr/>")
		fmt.Fprintf(w, "<b>Name:</b> %s<br/>", username)
		fmt.Fprintf(w, "<a href=\"/cgi-bin/go/go-sessions-2.cgi\">Session Page 2</a><br/>")
		fmt.Fprintf(w, "<a href=\"/hw2/go-cgiform.html\">Go CGI Form</a><br/>")
		fmt.Fprintf(w, "<form style=\"margin-top:30px\" action=\"/cgi-bin/go/go-destroy-session.cgi\" method=\"get\">")
		fmt.Fprintf(w, "<button type=\"submit\">Destroy Session</button>")
		fmt.Fprintf(w, "</form>")

		fmt.Fprintf(w, "</body>")
		fmt.Fprintf(w, "</html>")
	}))
}
