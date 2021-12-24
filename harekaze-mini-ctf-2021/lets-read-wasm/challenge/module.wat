;; wat2wasm --enable-simd --debug-names -o ./module.wasm ./module.wat
(module
  (memory $memory 1)
  (data (i32.const 0))
  (data (i32.const 1024) "\4d\60\00\00\56\61\00\00\63\70\00\00\53\6f\00\00\47\61\00\00\55\4b\00\00\7a\70\00\00\7a\6f\00\00")
  (data (i32.const 1056) "\6f\7c\00\00\63\74\00\00\63\40\00\00\74\77\00\00\78\71\00\00\7f\77\00\00\59\27\00\00\65\24\00\00")
  (data (i32.const 1088) "\6d\30\00\00\74\5c\00\00\55\76\00\00\7b\60\00\00\6c\73\00\00\2b\76\00\00\7a\74\00\00\54\6d\00\00")
  (data (i32.const 1120) "\36\7e\00\00\71\75\00\00\24\4f\00\00\75\61\00\00\78\7e\00\00\70\61\00\00\3d\36\00\00\74\74\00\00")

  (export "memory" (memory $memory))
  (export "check" (func $check))

  ;; compress v128
  (func $compress (param $x v128) (result i32)
    i32.const 0

    local.get $x
    i8x16.extract_lane_u 0
    i32.or
    local.get $x
    i8x16.extract_lane_u 1
    i32.or
    local.get $x
    i8x16.extract_lane_u 2
    i32.or
    local.get $x
    i8x16.extract_lane_u 3
    i32.or
    local.get $x
    i8x16.extract_lane_u 4
    i32.or
    local.get $x
    i8x16.extract_lane_u 5
    i32.or
    local.get $x
    i8x16.extract_lane_u 6
    i32.or
    local.get $x
    i8x16.extract_lane_u 7
    i32.or
    
    i32.const 8
    i32.shl

    local.get $x
    i8x16.extract_lane_u 8
    i32.or
    local.get $x
    i8x16.extract_lane_u 9
    i32.or
    local.get $x
    i8x16.extract_lane_u 10
    i32.or
    local.get $x
    i8x16.extract_lane_u 11
    i32.or
    local.get $x
    i8x16.extract_lane_u 12
    i32.or
    local.get $x
    i8x16.extract_lane_u 13
    i32.or
    local.get $x
    i8x16.extract_lane_u 14
    i32.or
    local.get $x
    i8x16.extract_lane_u 15
    i32.or)

  ;; check if a 16-byte block is correct
  (func $check_block (param $offset_input i32) (param $offset_enc i32) (result i32)
    (local $i i32)
    (local $mask i64)
    (local $block v128)
    (local $result i32)

    local.get $offset_input
    v128.load
    local.set $block

    ;; init vars
    i32.const 0
    local.set $result

    i64.const 0x8040201008040201 ;; mask
    local.set $mask
    i32.const 0
    local.set $i

    block $B0
      loop $L0
        ;; $block & $mask
        local.get $block
        local.get $mask
        i64x2.splat
        v128.and

        ;; compress!
        call $compress

        ;; compare!
        local.get $offset_enc
        local.get $i
        i32.const 2
        i32.shl
        i32.add
        i32.load ;; memory[$offset_enc+$i*4]
        
        i32.ne
        br_if $B0

        ;; rotl($mask, 1)
        local.get $mask
        i64.const 8
        i64.rotr
        local.set $mask

        ;; incr
        local.get $i
        i32.const 1
        i32.add

        ;; cond
        local.tee $i
        i32.const 8
        i32.ne
        br_if $L0
      end

      i32.const 1
      local.set $result
    end

    local.get $result)

  (func $check (result i32)
    (local $i i32)
    (local $result i32)

    ;; init vars
    i32.const 0
    local.set $result

    i32.const 0
    local.set $i

    block $B0
      loop $L0
        ;; calc $offset_input
        local.get $i
        i32.const 4
        i32.shl

        ;; calc $offset_enc
        local.get $i
        i32.const 5
        i32.shl
        i32.const 1024
        i32.add
        call $check_block
        
        i32.eqz
        br_if $B0

        ;; incr
        local.get $i
        i32.const 1
        i32.add

        ;; cond
        local.tee $i
        i32.const 4
        i32.ne
        br_if $L0
      end

      i32.const 1
      local.set $result
    end

    local.get $result)
)