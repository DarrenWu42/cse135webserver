package main

import (
	"fmt"
	"net/http"
	"net/http/cgi"
)

func main() {
	cgi.Serve(http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		header := w.Header()
		header.Set("Cache-Control", "no-cache")
		header.Set("Content-Type", "text/html")
		fmt.Fprintf(w, "<html><head><title>POST Message Body</title></head><body><h1 align=center>POST Message Body</h1><hr/>")

		r.ParseForm()
		post := r.PostForm

		fmt.Fprintf(w, "Message Body:<br/>")
		for k := range post {
			fmt.Fprintf(w, "<b>%s</b> : %s<br/>", k, post.Get(k))
		}

		fmt.Fprintf(w, "</body>")
		fmt.Fprintf(w, "</html>")
	}))
}
