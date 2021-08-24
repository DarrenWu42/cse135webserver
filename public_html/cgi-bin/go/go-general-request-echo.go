package main

import (
	"fmt"
	"net/http"
	"net/http/cgi"
)

func main() {
	cgi.Serve(http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		header := w.Header()
		fmt.Fprintf(w, "<html><head><title>General Request Echo</title></head><body><h1 align=center>General Request Echo</h1><hr/>")

		fmt.Fprintf(w, "<b>Protocol:</b> %s<br/>\n", r.Proto)
		fmt.Fprintf(w, "<b>Method:</b> %s<br/>\n", r.Method)
		fmt.Fprintf(w, "<b>Query String and/or Message Body:</b><br/>\n")

		r.ParseForm()
		form := r.Form
		for k := range form {
			fmt.Fprintf(w, "<b>%s</b> : %s<br/>", k, form.Get(k))
		}

		fmt.Fprintf(w, "</body>")
		fmt.Fprintf(w, "</html>")
	}))
}
