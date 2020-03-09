import base64
import re
import struct

def hash(s):
  result = 0
  for c in s:
    result *= 31
    result += c
  return result

user = b'i-am-misakiakeno'
code = base64.b32encode(user + struct.pack('<I', hash(user) & 0xffffffff)).decode()
code = code.translate(str.maketrans('ABCDEFGHIJKLMNOPQRSTUVWXYZ234567=', 'Y9ND6U0RXCPIOHQL418G7KAVJ3FW5BZTS'))
print('-'.join(re.findall(r'.{4}', code)))
