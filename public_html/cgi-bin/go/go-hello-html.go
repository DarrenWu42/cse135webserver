package main

import (
	"fmt"
	"os"
	"time"
)

func main() {
	fmt.Printf("Cache-Control: no-cache\n")
	fmt.Printf("Content-Type: text/html\n\n")
	fmt.Printf("<html><head><title>Hello, Go!</title></head>")
	fmt.Printf("<body><h1 align=center>Hello, Go!</h1><hr/>")
	fmt.Printf("Hello, World!<br/>")

	fmt.Printf("This program was generated at: %s<br/>", time.Now().Format("Mon Jul 16 02:03:55 1987"))
	fmt.Printf("Your current IP address is: %s<br/>", os.Getenv("REMOTE_ADDR"))

	fmt.Printf("</body></html>")
}
