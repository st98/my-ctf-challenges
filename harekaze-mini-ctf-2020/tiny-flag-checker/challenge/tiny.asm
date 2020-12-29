; references:
; - http://shinh.hatenablog.com/entry/20060911/1157921389
; - https://blog.stalkr.net/2014/10/tiny-elf-3264-with-nasm.html

BITS 64
  org 0x400000

ehdr:			; Elf64_Ehdr
key1:
  db 0x7f, "ELF"	; e_ident
  db "UUN_KAWAII", 0x16, 0xc6
  dw 2			; e_type
  dw 0x3e		; e_machine
  db "ABCD"			; e_version
  dq _start 		; e_entry
  dq phdr - $$		; e_phoff
key2:
  dq 0xf0fdcf637563fce7			; e_shoff
  db 0x66, 0xae, 0xdc, 0x4f			; e_flags
  db 0x4f, 0xcf		; e_ehsize
  dw phdrsize		; e_phentsize
  dw 1			; e_phnum
  dw 0			; e_shentsize
  dw 0			; e_shnum
  dw 0			; e_shstrndx
  ehdrsize equ $ - ehdr

phdr:			; Elf64_Phdr
  dd 1			; p_type
  dd 5			; p_flags
  dq 0			; p_offset
  dq $$			; p_vaddr
  nope db "Nope...", 0xa			; p_paddr
  dq filesize		; p_filesz
  dq filesize		; p_memsz
  input db "Input: ", 0x0		; p_align
  phdrsize equ $ - phdr

correct_prefix db "Correct! Flag: HarekazeCTF{"
correct_suffix db "}", 0xa

print:
  mov eax, 1
  mov edi, 1
  syscall
  ret

_start:
  sub rsp, 16
  mov r8, rsp
  sub rsp, 16
  mov r9, rsp
  pxor xmm0, xmm0
  movaps [rsp], xmm0
  movaps [rsp+16], xmm0

welcome:
  mov esi, input
  mov edx, 7
  call print

  xor eax, eax
  xor edi, edi
  mov rsi, r8
  mov edx, 16
  syscall

go:
  mov rax, [r8]
  xor qword [r9], rax
  mov rax, [r8+8]
  xor qword [r9+8], rax

  ror qword [r9], 41
  ror qword [r9+8], 19

  mov rax, [key1]
  xor [r9], rax
  mov rax, [key1+8]
  xor [r9+8], rax

  mov rax, [key2]
  xor rax, [r9]
  mov rdx, [key2+8]
  xor rdx, [r9+8]

  and rax, rax
  jnz fail
  cmp rax, rdx
  jne fail

congratz:
  mov esi, correct_prefix
  mov edx, 27
  call print

  mov rsi, r8
  mov edx, 16
  call print

  mov esi, correct_suffix
  mov edx, 2
  call print
  jmp end

fail:
  mov esi, nope
  mov edx, 8
  call print

end:
  mov eax, 60
  xor edi, edi
  syscall

filesize equ $ - $$
