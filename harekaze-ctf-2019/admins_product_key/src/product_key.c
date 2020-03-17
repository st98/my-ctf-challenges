#include <stdio.h>
#include <stdlib.h>
#include <string.h>

unsigned int hash(char *s, int len) {
  unsigned int result = 0;
  int i;
  for (i = 0; i < len; i++) {
    result *= 31;
    result += s[i];
  }
  return result;
}

// Base32
char *table = "Y9ND6U0RXCPIOHQL418G7KAVJ3FW5BZT";
char padding = 'S';
unsigned char inv_table[256] = {};
void decode(char *input, unsigned char *output) {
  int len = strlen(input);
  int output_len = len * 5 / 8;
  int i;
  char *p = input;

  for (i = 0; i < len; i++) {
    if (input[i] == padding) {
      input[i] = table[0];
    }
  }

  for (i = 0; i < output_len; i++) {
    switch (i % 5) {
    case 0:
      output[i] = (inv_table[*p] << 3) | (inv_table[*(p + 1)] >> 2);
      p++;
      break;
    case 1:
      output[i] = ((inv_table[*p] & 3) << 6) | (inv_table[*(p + 1)] << 1) | (inv_table[*(p + 2)] >> 4);
      p += 2;
      break;
    case 2:
      output[i] = ((inv_table[*p] & 0xf) << 4) | (inv_table[*(p + 1)] >> 1);
      p++;
      break;
    case 3:
      output[i] = ((inv_table[*p] & 1) << 7) | (inv_table[*(p + 1)] << 2) | (inv_table[*(p + 2)] >> 3);
      p += 2;
      break;
    case 4:
      output[i] = ((inv_table[*p] & 7) << 5) | inv_table[*(p + 1)];
      p += 2;
      break;
    }
  }

  output[output_len] = '\0';
}

void init_table(char *table) {
  int len = strlen(table);
  for (int i = 0; i < len; i++) {
    inv_table[table[i]] = i;
  }
}

typedef struct _member {
  char name[16];
  unsigned int hash;
} member;

int main(int argc, char **argv) {
  char product_key[33] = {};
  unsigned char output[32] = {};
  char *token;
  member *user;

  if (argc < 2) {
    fprintf(stderr, "Usage: %s <product key>\n", argv[0]);
    return EXIT_FAILURE;
  }

  if (strlen(argv[1]) != 39) {
    fprintf(stderr, "invalid product key\n");
    return EXIT_FAILURE;
  }

  token = strtok(argv[1], "-");
  while (token != NULL) {
    strncat(product_key, token, 4);
    token = strtok(NULL, "-");
  }

  init_table(table);
  decode(product_key, output);
  user = (member *) output; 

  if (hash(user->name, 16) != user->hash) {
    fprintf(stderr, "invalid product key\n");
    return EXIT_FAILURE;
  }

  printf("Hello, %.16s!\n", user->name);
  if (strncmp(user->name, "i-am-misakiakeno", 16) == 0) {
    printf("You are admin! The flag is: HarekazeCTF{%s}\n", product_key);
  } else {
    puts("You are not admin.");
  }

  return EXIT_SUCCESS;
}