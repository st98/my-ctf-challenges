s = b"fakeflag{this_is_not_the_real_flag}"
t = b"HarekazeCTF{0rth0d0x_fl4g_ch3ck3r!}"

res = []
funcs = [
  lambda a, b: b - a,
  lambda a, b: a - b,
  lambda a, b: a ^ b
]
for i, (c, d) in enumerate(zip(s, t)):
  res.append(funcs[i % 3](c, d))

print(res)
