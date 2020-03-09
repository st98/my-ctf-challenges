import struct

with open('scramble', 'rb') as f:
  s = f.read()

encrypted = list(s[0x1020:0x1046])
table = struct.unpack('<' + 'I' * 266, s[0x1060:0x1488])
for i, j in zip(range(len(table) - 1, -1, -1), table[::-1]):
  a, b = encrypted[i // 7], encrypted[j // 7]
  c, d = i % 7, j % 7

  encrypted[i // 7] &= ~(1 << c)
  encrypted[i // 7] |= ((b & (1 << d)) >> d) << c

  encrypted[j // 7] &= ~(1 << d)
  encrypted[j // 7] |= ((a & (1 << c)) >> c) << d

print(''.join(chr(c) for c in encrypted))