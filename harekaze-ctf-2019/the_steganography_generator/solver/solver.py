import binascii
import os
import os.path
import sys
from PIL import Image

MAGIC_NUMBER = (127, 115, 116, 101, 103, 97, 110, 111);

def random_generator(seed):
  state = (seed ^ 0x5deece66d) & ((1 << 48) - 1)

  while True:
    state = (0x5deece66d * state + 0xb) & ((1 << 48) - 1)
    yield state >> 17

def extract_byte(pix, seed, i):
  rng = random_generator(seed)
  used = set()
  t = 0

  for j in range(8):
    x = next(rng) % w
    while True:
      x = (x + 1) % w
      if x not in used:
        break
    used.add(x)

    r, g, b, a = pix[x, i]
    if r & 1:
      t |= 1 << j

  return t

passwords = []
def attempt(pix, password, prev=0):
  i = len(password)
  if i == 8:
    passwords.append(password)
    return

  for c in range(256):
    if extract_byte(pix, c ^ i * i * prev, i) == MAGIC_NUMBER[i]:
      attempt(pix, password + bytes([c]), c)

if __name__ == '__main__':
  if len(sys.argv) < 2:
    print('usage: python {} <stego file>'.format(sys.argv[0]))
    sys.exit(1)

  im = Image.open(sys.argv[1])
  w, h = im.size
  pix = im.load()
  prev = 0

  # determine password
  attempt(pix, b'')
  print('passwords:', passwords)

  if not os.path.exists('result'):
    os.mkdir('result')

  # extract embedded file
  for password in passwords:
    result = b''
    for i in range(h):
      seed = password[i % len(password)]
      seed ^= i * i * password[(i + len(password) - 1) % len(password)]
      result += bytes([extract_byte(pix, seed, i)])

    with open('result/result-{}.bin'.format(binascii.hexlify(password).decode()), 'wb') as f:
      f.write(result[8:])