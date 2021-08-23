package main

import (
	"encoding/json"
	"fmt"
	"os"
	"time"
)

type response1 struct {
	Message string
	Date    string
	IP      string
}

func main() {
	fmt.Printf("Cache-Control: no-cache\n")
	fmt.Printf("Content-Type: application/json\n\n")

	response := response1{
		Message: "Hello, GO!",
		Date:    time.Now().Format("Mon Jul 16 02:03:55 1987"),
		IP:      os.Getenv("REMOTE_ADDR"),
	}

	var jsonData []byte
	jsonData, err := json.Marshal(response)
	_ = err
	fmt.Println(string(jsonData))
}
