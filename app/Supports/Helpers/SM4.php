<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-09-18
 * Time: 19:20
 */

namespace App\Supports\Helpers;


class SM4
{
    const SM4_CK = [
        0x00070e15, 0x1c232a31, 0x383f464d, 0x545b6269,
        0x70777e85, 0x8c939aa1, 0xa8afb6bd, 0xc4cbd2d9,
        0xe0e7eef5, 0xfc030a11, 0x181f262d, 0x343b4249,
        0x50575e65, 0x6c737a81, 0x888f969d, 0xa4abb2b9,
        0xc0c7ced5, 0xdce3eaf1, 0xf8ff060d, 0x141b2229,
        0x30373e45, 0x4c535a61, 0x686f767d, 0x848b9299,
        0xa0a7aeb5, 0xbcc3cad1, 0xd8dfe6ed, 0xf4fb0209,
        0x10171e25, 0x2c333a41, 0x484f565d, 0x646b7279
    ];

    const SM4_SBOX = [
        0xd6,0x90,0xe9,0xfe,0xcc,0xe1,0x3d,0xb7,0x16,0xb6,0x14,0xc2,0x28,0xfb,0x2c,0x05,
        0x2b,0x67,0x9a,0x76,0x2a,0xbe,0x04,0xc3,0xaa,0x44,0x13,0x26,0x49,0x86,0x06,0x99,
        0x9c,0x42,0x50,0xf4,0x91,0xef,0x98,0x7a,0x33,0x54,0x0b,0x43,0xed,0xcf,0xac,0x62,
        0xe4,0xb3,0x1c,0xa9,0xc9,0x08,0xe8,0x95,0x80,0xdf,0x94,0xfa,0x75,0x8f,0x3f,0xa6,
        0x47,0x07,0xa7,0xfc,0xf3,0x73,0x17,0xba,0x83,0x59,0x3c,0x19,0xe6,0x85,0x4f,0xa8,
        0x68,0x6b,0x81,0xb2,0x71,0x64,0xda,0x8b,0xf8,0xeb,0x0f,0x4b,0x70,0x56,0x9d,0x35,
        0x1e,0x24,0x0e,0x5e,0x63,0x58,0xd1,0xa2,0x25,0x22,0x7c,0x3b,0x01,0x21,0x78,0x87,
        0xd4,0x00,0x46,0x57,0x9f,0xd3,0x27,0x52,0x4c,0x36,0x02,0xe7,0xa0,0xc4,0xc8,0x9e,
        0xea,0xbf,0x8a,0xd2,0x40,0xc7,0x38,0xb5,0xa3,0xf7,0xf2,0xce,0xf9,0x61,0x15,0xa1,
        0xe0,0xae,0x5d,0xa4,0x9b,0x34,0x1a,0x55,0xad,0x93,0x32,0x30,0xf5,0x8c,0xb1,0xe3,
        0x1d,0xf6,0xe2,0x2e,0x82,0x66,0xca,0x60,0xc0,0x29,0x23,0xab,0x0d,0x53,0x4e,0x6f,
        0xd5,0xdb,0x37,0x45,0xde,0xfd,0x8e,0x2f,0x03,0xff,0x6a,0x72,0x6d,0x6c,0x5b,0x51,
        0x8d,0x1b,0xaf,0x92,0xbb,0xdd,0xbc,0x7f,0x11,0xd9,0x5c,0x41,0x1f,0x10,0x5a,0xd8,
        0x0a,0xc1,0x31,0x88,0xa5,0xcd,0x7b,0xbd,0x2d,0x74,0xd0,0x12,0xb8,0xe5,0xb4,0xb0,
        0x89,0x69,0x97,0x4a,0x0c,0x96,0x77,0x7e,0x65,0xb9,0xf1,0x09,0xc5,0x6e,0xc6,0x84,
        0x18,0xf0,0x7d,0xec,0x3a,0xdc,0x4d,0x20,0x79,0xee,0x5f,0x3e,0xd7,0xcb,0x39,0x48
    ];

    const SM4_FK = [0xA3B1BAC6, 0x56AA3350, 0x677D9197, 0xB27022DC];

    public $_rk = [];
    public $_block_size = 16;
    //$key限定16位
    public function encrypt($key, $data)
    {
        $this->sM4KeySchedule($key);

        $bytes = $this->pad($data, $this->_block_size);
        $chunks = array_chunk($bytes, $this->_block_size);

        $ciphertext = "";
        foreach ($chunks as $chunk) {
            $ciphertext .= $this->sM4Encrypt($chunk);
        }

        return base64_encode($ciphertext);
    }

    public function decrypt($key, $data)
    {
        $data = base64_decode($data);
        if (strlen($data) < 0 || strlen($data) % $this->_block_size != 0) {
            return false;
        }

        $this->sM4KeySchedule($key);
        $bytes = unpack("C*", $data);
        $chunks = array_chunk($bytes, $this->_block_size);

        $plaintext = "";
        foreach ($chunks as $chunk) {
            $plaintext .= substr($this->sM4Decrypt($chunk), 0, 16);
        }
        $plaintext = $this->un_pad($plaintext);

        return $plaintext;
    }

    private function sM4Decrypt($cipherText)
    {
        $x = [];
        for ($j=0; $j<4; $j++) {
            $x[$j]=($cipherText[$j*4]<<24)  |($cipherText[$j*4+1]<<16)| ($cipherText[$j*4+2]<<8)|($cipherText[$j*4+3]);
        }

        for ($i=0; $i<32; $i++) {
            $tmp = $x[$i+1]^$x[$i+2]^$x[$i+3]^$this->_rk[31-$i];
            $buf= (self::SM4_SBOX[($tmp >> 24) & 0xFF]) << 24 |(self::SM4_SBOX[($tmp >> 16) & 0xFF]) << 16 |(self::SM4_SBOX[($tmp >> 8) & 0xFF]) << 8 |(self::SM4_SBOX[$tmp & 0xFF]);
            $x[$i+4]=$x[$i]^($buf^$this->sm4Rotl32(($buf), 2)^ $this->sm4Rotl32(($buf), 10) ^ $this->sm4Rotl32(($buf), 18)^ $this->sm4Rotl32(($buf), 24));
        }

        $plainText = [];
        for ($k=0; $k<4; $k++) {
            $plainText[4*$k]=($x[35-$k]>> 24)& 0xFF;
            $plainText[4*$k+1]=($x[35-$k]>> 16)& 0xFF;
            $plainText[4*$k+2]=($x[35-$k]>> 8)& 0xFF;
            $plainText[4*$k+3]=($x[35-$k])& 0xFF;
        }

        return $this->bytesToString($plainText);
    }

    private function sM4Encrypt($plainText)
    {
        $x = [];
        for ($j=0; $j<4; $j++) {
            $x[$j]=($plainText[$j*4]<<24)  |($plainText[$j*4+1]<<16)| ($plainText[$j*4+2]<<8)|($plainText[$j*4+3]);
        }

        for ($i=0; $i<32; $i++) {
            $tmp = $x[$i+1]^$x[$i+2]^$x[$i+3]^$this->_rk[$i];
            $buf= (self::SM4_SBOX[($tmp >> 24) & 0xFF]) << 24 |(self::SM4_SBOX[($tmp >> 16) & 0xFF]) << 16 |(self::SM4_SBOX[($tmp >> 8) & 0xFF]) << 8 |(self::SM4_SBOX[$tmp & 0xFF]);
            $x[$i+4]=$x[$i]^($buf^$this->sm4Rotl32(($buf), 2)^ $this->sm4Rotl32(($buf), 10) ^ $this->sm4Rotl32(($buf), 18)^ $this->sm4Rotl32(($buf), 24));
        }

        $cipherText = [];
        for ($k=0; $k<4; $k++) {
            $cipherText[4*$k]=($x[35-$k]>> 24)& 0xFF;
            $cipherText[4*$k+1]=($x[35-$k]>> 16)& 0xFF;
            $cipherText[4*$k+2]=($x[35-$k]>> 8)& 0xFF;
            $cipherText[4*$k+3]=($x[35-$k])& 0xFF;
        }

        return $this->bytesToString($cipherText);
    }

    private function stringToBytes($string)
    {
        return unpack('C*', $string);
    }

    private function bytesToString($bytes)
    {
        return vsprintf(str_repeat('%c', count($bytes)), $bytes);
    }

    private function pad($data)
    {
        $bytes = $this->stringToBytes($data);
        $rem = $this->_block_size - count($bytes) % $this->_block_size;
        for ($i = 0; $i < $rem; $i++) {
            array_push($bytes, $rem);
        }
        return $bytes;
    }

    private function un_pad($data)
    {
        $bytes = $this->stringToBytes($data);
        $rem = $bytes[count($bytes)];
        $bytes = array_slice($bytes, 0, count($bytes) - $rem);
        return $this->bytesToString($bytes);
    }

    private function sm4Rotl32($buf, $n)
    {
        return (($buf << $n) & 0xffffffff) | ($buf >> (32-$n));
    }

    private function sM4KeySchedule($key)
    {

        $this->_rk = [];
        $key = array_values(unpack("C*", $key));
        $k = [];
        for ($i=0; $i<4; $i++) {
            $k[$i] = self::SM4_FK[$i]^(($key[4*$i]<<24) | ($key[4*$i+1]<<16) |($key[4*$i+2]<<8) | ($key[4*$i+3]));
        }

        for ($j=0; $j<32; $j++) {
            $tmp = $k[$j+1]^$k[$j+2]^$k[$j+3]^ self::SM4_CK[$j];
            $buf = (self::SM4_SBOX[($tmp >> 24) & 0xFF]) << 24 |(self::SM4_SBOX[($tmp >> 16) & 0xFF]) << 16 |(self::SM4_SBOX[($tmp >> 8) & 0xFF]) << 8 |(self::SM4_SBOX[$tmp & 0xFF]);

            $k[$j+4]=$k[$j]^(($buf)^($this->sm4Rotl32(($buf), 13))^($this->sm4Rotl32(($buf), 23)));
            $this->_rk[$j]=$k[$j+4];
        }
    }
}
