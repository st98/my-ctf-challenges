import binascii
import struct

def decode(s):
  res = [0 for _ in range(16)]

  for i in range(8):
    for j in range(8):
      res[i] |= (s[j] & 1 << ((i + j) % 8 + 8)) >> 8
  for i in range(8, 16):
    for j in range(8):
      res[i] |= s[j] & 1 << ((i + j) % 8)

  return ''.join(chr(c) for c in res)

enc = '''
4D 60 00 00 56 61 00 00 63 70 00 00 53 6F 00 00 47 61 00 00 55 4B 00 00 7A 70 00 00 7A 6F 00 00
6F 7C 00 00 63 74 00 00 63 40 00 00 74 77 00 00 78 71 00 00 7F 77 00 00 59 27 00 00 65 24 00 00
6D 30 00 00 74 5C 00 00 55 76 00 00 7B 60 00 00 6C 73 00 00 2B 76 00 00 7A 74 00 00 54 6D 00 00
36 7E 00 00 71 75 00 00 24 4F 00 00 75 61 00 00 78 7E 00 00 70 61 00 00 3D 36 00 00 74 74 00 00
'''.strip().splitlines()
enc = [binascii.unhexlify(line.replace(' ', '')) for line in enc]
enc = [struct.unpack('<IIIIIIII', line) for line in enc]

flag = ''.join(decode(line) for line in enc)
print(flag)