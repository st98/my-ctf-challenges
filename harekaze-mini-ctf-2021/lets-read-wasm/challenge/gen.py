import binascii
import struct

flag = b'HarekazeCTF{I_wr0te_web4ssembly_text_f0rm4t_by_h4nd_tsuk4ret4y0}'
print('len:', len(flag))

def f(x):
  res = 0
  for i in range(8):
    res |= x[i] << 8
  for i in range(8, 16):
    res |= x[i]
  return res

def mask(s, x):
  return [c & (1 << ((i + x) % 8)) for i, c in enumerate(s)]

def encode(a):
  res = b''.join(struct.pack('<I', x) for x in a)
  return ''.join('\\{:02x}'.format(c) for c in res)

for i in range(0, len(flag), 16):
  tmp = flag[i:i+16]
  tmp2 = [f(mask(tmp, j)) for j in range(8)]
  print(encode(tmp2))