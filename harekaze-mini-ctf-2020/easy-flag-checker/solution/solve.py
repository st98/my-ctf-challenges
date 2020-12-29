import struct

us = lambda x: struct.unpack('b', x)[0]

with open('chall', 'rb') as f:
  f.seek(0x3060)

  flag = ''
  for i, c in enumerate(b'fakeflag{this_is_not_the_real_flag}'):
    d = f.read(1)
    if i % 3 == 0:
      flag += chr(c + us(d))
    elif i % 3 == 1:
      flag += chr(c - us(d))
    elif i % 3 == 2:
      flag += chr(c ^ ord(d))

  print(flag)
