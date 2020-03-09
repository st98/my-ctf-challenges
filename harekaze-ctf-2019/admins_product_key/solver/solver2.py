# gdb -n -q -x solver.py ./product_key
import gdb
import re
import struct

def hash(s, n):
  res = 0
  for i in range(n):
    res = (res << 5) - res + ord(s[i])
  return res & 0xffffffff

def str_to_bin(s):
  return ''.join(bin(ord(c))[2:].zfill(8) for c in s)

def hyphenate(s, n):
  return '-'.join(re.findall(r'.{%d}|.+' % n, s))

def p32(x):
  return struct.pack('<I', x)

TABLE = 'Y9ND6U0RXCPIOHQL418G7KAVJ3FW5BZT'
USER = 'i-am-misakiakeno'

target = USER + p32(hash(USER, 16))
target = str_to_bin(target)

gdb.execute('set pagination off')
gdb.execute('b *(main + 0x127)', to_string=True)

key = ''
for i in range(0, len(target), 5):
  for c in TABLE:
    tmp = hyphenate((key + c).ljust(32, 'S'), 4)
    gdb.execute('r ' + tmp, to_string=True)
    res = gdb.execute('x/20bx $rsp', to_string=True)
    res = ''.join(re.findall(r'0x([0-9a-f]{2})[^0-9a-f]', res)).decode('hex')
    res = str_to_bin(res)
    if res[i:i+5] == target[i:i+5]:
      key += c
      break
  print '[+]', key

print '[+] Product Key:', hyphenate(key, 4)
print '[+] Flag: HarekazeCTF{%s}' % key
gdb.execute('continue', to_string=True)
gdb.execute('quit')