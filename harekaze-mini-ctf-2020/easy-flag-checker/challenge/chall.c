#include <stdio.h>

char add(char a, char b) {
  return a + b;
}

char sub(char a, char b) {
  return a - b;
}

char xor(char a, char b) {
  return a ^ b;
}

char (*funcs[3])(char a, char b) = {
  add, sub, xor
};
char table[35] = {
  -30, 0, 25, 0, -5, 13, 25, 2, 56, -32, 34, 18, -67, -19, 29, -11, 47, 10, -63, -4, 0, -14, -4, 81, 8, 19, 6, 7, 57, 60, 5, 57, 19, -70, 0
};
int check(const char *s1, const char *s2) {
  int i;
  char c;

  for (i = 0; i < 35; i++) {
    c = funcs[i % 3](s2[i], table[i]);
    if (s1[i] > c) return 1;
    if (s1[i] < c) return -1;
  }

  return 0;
}

int main(void) {
  char input[36];
  printf("Input flag: ");
  scanf("%35s", input);

  if (check(input, "fakeflag{this_is_not_the_real_flag}") == 0) {
    printf("Congratulations! The flag is: %s\n", input);
  } else {
    puts("Nope.");
  }

  return 0;
}
