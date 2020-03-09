# [Reversing 200] Admin's Product key
## Description
I forgot admin's product key…

- [product_key](attachments/product_key)

## Intended Solution
### Static Analysis
- The binary uses custom Base32 (table is changed to `Y9ND6U0RXCPIOHQL418G7KAVJ3FW5BZTS`) to decode user input. Decoded bytes are composed of 16 bytes (username) + 4 bytes (hash of username) as follows.

```c
typedef struct {
  char name[16];
  unsigned int hash;
} member;
```

- The hash is calculated by the following algorithm.

```python
def hash(s):
  result = 0
  for c in s:
    result *= 31
    result += c
  return result
```

- So, by writing script that calculates `encode("i-am-misakiakeno") + hash(encode("i-am-misakiakeno"))`, you can get the key to get the flag.
- solver: [solver1.py](solver/solver1.py)

### Dynamic Analysis
- solver: [solver2.py](solver/solver2.py)

## Flag
```
HarekazeCTF{H6AANWCHHK7V0JIIHU4AA3IQHBWTV514}
```