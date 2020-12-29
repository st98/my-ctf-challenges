# objdump -D -b binary -m i386:x86-64 -M intel tiny > disas.txt
import binascii
import struct

def rol(x, shift):
  return ((x << shift) | (x >> (64 - shift))) & 0xffffffffffffffff

def unhex(x):
  return binascii.unhexlify(hex(x)[2:])[::-1]

with open('tiny', 'rb') as f:
  elf = f.read()

k1, k2 = struct.unpack('QQ', elf[:0x10])
k3, k4 = struct.unpack('QQ', elf[0x28:0x38])

flag = ''
flag += unhex(rol(k1 ^ k3, 0x29)).decode()
flag += unhex(rol(k2 ^ k4, 0x13)).decode()
print(flag)