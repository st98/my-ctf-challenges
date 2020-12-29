def ror(x, shift):
  return ((x >> shift) | (x << (64 - shift))) & 0xffffffffffffffff

f = lambda s: int(s[::-1].encode('hex'), 16)

x = f('fl4g_1s_')
k1 = f('\x7fELFUUN_')
print(hex(ror(x, 41) ^ k1))

y = f('t1ny_t00')
k2 = f('KAWAII\x16\xc6')
print(hex(ror(y, 19) ^ k2))
