package main

import (
	"fmt"
	"os"
)

func main() {
	fmt.Printf("Cache-Control: no-cache\n")
	fmt.Printf("Content-type: text/html\n\n")
	fmt.Printf("<html><head><title>Environment Variables</title></head><body><h1 align=center>Environment Variables</h1><hr/>\n")
	for _, e := range os.Environ() {
		fmt.Printf(e + "\n<br/>")
	}
	fmt.Printf("</body></html>")
}
