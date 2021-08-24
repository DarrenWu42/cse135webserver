package main

import (
	"fmt"
	"net/http"
	"net/http/cgi"
)

func main() {
	cgi.Serve(http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		header := w.Header()

		cookie := http.Cookie{Name: "username", Value: "None"} // create cookie
		http.SetCookie(w, &cookie)                             // set cookie to response

		header.Set("Cache-Control", "no-cache")
		header.Set("Content-Type", "text/html")

		fmt.Fprintf(w, "<html><head><title>Go Session Destroyed</title></head><body><h1>Go Session Destroyed</h1><hr/>")
		fmt.Fprintf(w, "<a href=\"/hw2/go-cgiform.html\">Back to the Go CGI Form</a><br/>")
		fmt.Fprintf(w, "<a href=\"/cgi-bin/go/go-sessions-1.cgi\">Session Page 1</a><br />")
		fmt.Fprintf(w, "<a href=\"/cgi-bin/go/go-sessions-2.cgi\">Session Page 2</a><br/>")
		fmt.Fprintf(w, "</body>")
		fmt.Fprintf(w, "</html>")
	}))
}
