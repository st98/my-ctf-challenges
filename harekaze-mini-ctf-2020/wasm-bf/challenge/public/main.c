#define WASM_EXPORT __attribute__((visibility("default")))

#define BUFFER_SIZE 100
#define MEMORY_SIZE 1000
#define PROGRAM_MAX_SIZE 1000

extern void _print_char(char c);
extern char _get_char(void);
extern void _flush(void);

unsigned char buffer[BUFFER_SIZE] = {0};
unsigned char *buffer_pointer = buffer;
unsigned char memory[MEMORY_SIZE] = {0};
char program[PROGRAM_MAX_SIZE] = {0};

void print_string(unsigned char *s) {
  unsigned char c;
  while (c = *s++) {
    _print_char(c);
  }
}

void flush(void) {
  print_string(buffer);
  _flush();

  buffer_pointer = buffer;
  for (int i = 0; i < BUFFER_SIZE; i++) {
    buffer[i] = '\0';
  }
}

void print_char(char c) {
  if (buffer_pointer + 4 >= buffer + BUFFER_SIZE) {
    flush();
  }

  // Prevent XSS!
  if (c == '<' || c == '>') {
    buffer_pointer[0] = '&';
    buffer_pointer[1] = c == '<' ? 'l' : 'g';
    buffer_pointer[2] = 't';
    buffer_pointer[3] = ';';
    buffer_pointer += 4;
  } else {
    *buffer_pointer = c;
    buffer_pointer++;
  }
}

WASM_EXPORT
void initialize() {
  buffer_pointer = buffer;
  for (int i = 0; i < BUFFER_SIZE; i++) {
    buffer[i] = '\0';
  }
  for (int i = 0; i < MEMORY_SIZE; i++) {
    memory[i] = '\0';
  }
  for (int i = 0; i < PROGRAM_MAX_SIZE; i++) {
    program[i] = '\0';
  }
}

WASM_EXPORT
int execute(int length) {
  for (int i = 0; i < length; i++) {
    program[i] = _get_char();
  }

  int pointer = 0;
  int counter = 0;
  int executed = 0;
  int depth = 0;

  while (counter < length && executed < 100000) {
    char c = program[counter];

    switch (c) {
      case '>': {
        pointer++;
        break;
      }
      case '<': {
        pointer--;
        break;
      }
      case '+': {
        memory[pointer]++;
        break;
      }
      case '-': {
        memory[pointer]--;
        break;
      }
      case '[': {
        if (memory[pointer] == 0) {
          for (depth = 0, counter++; program[counter] != ']' || depth > 0; counter++) {
            if (program[counter] == '[') depth++;
            if (program[counter] == ']') depth--; 
          }
        }
        break;
      }
      case ']': {
        if (memory[pointer] != 0) {
          for (depth = 0, counter--; program[counter] != '[' || depth > 0; counter--) {
            if (program[counter] == ']') depth++;
            if (program[counter] == '[') depth--;
          }
        }
        break;
      }
      case '.': {
        print_char(memory[pointer]);
        break;
      }
    }

    counter++;
    executed++;
  }

  end:
  flush();
  return 0;
}