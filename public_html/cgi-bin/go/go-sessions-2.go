package main

import (
	"fmt"
	"net/http"
	"net/http/cgi"
)

func main() {
	cgi.Serve(http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		header := w.Header()

		cookie, _ := r.Cookie("username") // get cookie from request
		username := cookie.Value          // get cookie value from cookie

		header.Set("Cache-Control", "no-cache")
		header.Set("Content-Type", "text/html")

		fmt.Fprintf(w, "<html><head><title>Go Sessions</title></head><body><h1>Go Page 2</h1><hr/>")
		fmt.Fprintf(w, "<b>Name:</b> %s<br/>", username)
		fmt.Fprintf(w, "<a href=\"/cgi-bin/go/go-sessions-1.cgi\">Session Page 1</a><br/>")
		fmt.Fprintf(w, "<a href=\"/hw2/go-cgiform.html\">Go CGI Form</a><br/>")
		fmt.Fprintf(w, "<form style=\"margin-top:30px\" action=\"/cgi-bin/go/go-destroy-session.cgi\" method=\"get\">")
		fmt.Fprintf(w, "<button type=\"submit\">Destroy Session</button>")
		fmt.Fprintf(w, "</form>")

		fmt.Fprintf(w, "</body>")
		fmt.Fprintf(w, "</html>")
	}))
}
