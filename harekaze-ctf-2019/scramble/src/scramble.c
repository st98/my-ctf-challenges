#include <stdio.h>
#include <string.h>

#define FLAG_LENGTH (38)
#define FLAG_BIT_LENGTH (7 * (FLAG_LENGTH))

int table[FLAG_BIT_LENGTH] = {
  169, 193, 264, 154, 254, 76, 231, 24, 215, 32, 221, 52, 48, 152, 243, 252, 56, 190, 1, 143, 194, 41, 43, 257, 187, 50, 29, 137, 226, 141, 156, 19, 64, 242, 121, 54, 181, 110, 167, 147, 192, 116, 83, 26, 230, 259, 91, 123, 232, 174, 129, 99, 222, 176, 140, 12, 133, 178, 20, 216, 75, 234, 262, 5, 102, 220, 245, 209, 125, 10, 69, 88, 217, 163, 150, 47, 237, 3, 23, 42, 4, 97, 244, 213, 203, 11, 86, 214, 51, 253, 36, 71, 96, 229, 81, 60, 131, 82, 196, 31, 148, 223, 239, 210, 235, 57, 14, 170, 80, 247, 74, 219, 90, 158, 15, 171, 145, 17, 261, 95, 62, 179, 258, 84, 114, 198, 55, 144, 107, 228, 138, 173, 13, 25, 139, 211, 249, 142, 146, 165, 260, 61, 37, 149, 204, 185, 44, 236, 164, 33, 201, 188, 59, 115, 241, 124, 35, 30, 240, 66, 263, 65, 53, 160, 227, 250, 120, 189, 180, 104, 157, 100, 161, 22, 132, 136, 112, 195, 151, 108, 67, 238, 109, 135, 155, 118, 202, 9, 159, 111, 103, 191, 246, 39, 255, 72, 184, 225, 38, 21, 126, 98, 134, 105, 119, 63, 94, 40, 122, 206, 87, 77, 73, 79, 8, 92, 18, 205, 218, 49, 58, 70, 6, 177, 233, 200, 208, 101, 251, 162, 248, 68, 106, 197, 130, 186, 207, 128, 182, 7, 117, 127, 172, 153, 175, 113, 16, 224, 45, 93, 85, 168, 78, 212, 183, 89, 46, 265, 27, 199, 256, 0, 166, 2, 34, 28
};
unsigned char encrypted[FLAG_LENGTH] = {
  31, 124, 44, 70, 47, 68, 2, 14, 108, 41, 42, 115, 121, 49, 4, 27, 80, 110, 107, 110, 52, 61, 39, 119, 122, 109, 88, 18, 57, 108, 47, 127, 35, 11, 44, 90, 119, 61
};

void scramble(unsigned char *s) {
  int i, a, b, c, d;
  for (i = 0; i < FLAG_BIT_LENGTH; i++) {
    a = s[i / 7];
    b = s[table[i] / 7];
    c = i % 7;
    d = table[i] % 7;
    s[i / 7] &= ~(1 << c);
    s[i / 7] |= ((b & (1 << d)) >> d) << c;
    s[table[i] / 7] &= ~(1 << d);
    s[table[i] / 7] |= ((a & (1 << c)) >> c) << d;
  }
}
int main(void) {
  unsigned char input[FLAG_LENGTH + 1];

  printf("Input : ");
  scanf("%38s", input);

  scramble(input);

  if (memcmp(input, encrypted, FLAG_LENGTH) == 0) {
    printf("Correct!\n");
  } else {
    puts("Nope.");
  }

  return 0;
}