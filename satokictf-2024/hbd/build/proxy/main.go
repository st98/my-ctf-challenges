package main

import (
	"bytes"
	"io"
	"net/http"
	"net/http/httputil"
	"net/url"
	"strconv"
	"os"
)

func getFlag() string {
	v := os.Getenv("FLAG")
	if len(v) == 0 {
		return "flag{dummy}"
	}
	return v
}

func modify(r *http.Response) error {
	body, err := io.ReadAll(r.Body)
	if err != nil {
		return err
	}

	var b []byte
	if bytes.Contains(body, []byte("HBD!Satoki!")) {
		b = []byte(getFlag())		
	} else {
		b = body
	}

	r.Body = io.NopCloser(bytes.NewReader(b))
	r.Header.Set("Content-Length", strconv.Itoa(len(b)))

	return nil
}

func main() {
	url, _ := url.Parse("http://apache")
	proxy := httputil.NewSingleHostReverseProxy(url)
	proxy.ModifyResponse = modify
	http.ListenAndServe(":8000", proxy)
}
