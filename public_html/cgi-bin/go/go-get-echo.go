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
		fmt.Fprintf(w, "<html><head><title>GET Request Echo</title></head><body><h1 align=center>GET Request Echo</h1><hr/>")

		// Get and format query string
		query := r.URL.Query()
		fmt.Fprintf(w, "Raw query string: %s<br/>", query)
		fmt.Fprintf(w, "Formatted Query String:<br/>")
		for k := range query {
			fmt.Fprintf(w, "<b>%s</b> : %s<br/>", k, query.Get(k))
		}

		// Print HTML footer
		fmt.Fprintf(w, "</body>")
		fmt.Fprintf(w, "</html>")
	}))
}
