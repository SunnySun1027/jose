<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2017 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace Jose\Component\Encryption\Util;

use Jose\Component\Core\Util\BigInteger;
use Jose\Component\Core\Util\RSAKey;

final class RSACrypt
{
    public static function encryptWithRSA15(RSAKey $key, string $data)
    {
        $mLen = mb_strlen($data, '8bit');

        if ($mLen > $key->getModulusLength() - 11) {
            throw new \InvalidArgumentException('Message too long');
        }

        $psLen = $key->getModulusLength() - $mLen - 3;
        $ps = '';
        while (mb_strlen($ps, '8bit') !== $psLen) {
            $temp = random_bytes($psLen - mb_strlen($ps, '8bit'));
            $temp = str_replace("\x00", '', $temp);
            $ps .= $temp;
        }
        $type = 2;
        $data = chr(0).chr($type).$ps.chr(0).$data;

        $data = BigInteger::createFromBinaryString($data);
        $c = self::getRSAEP($key, $data);
        $c = self::convertIntegerToOctetString($c, $key->getModulusLength());

        return $c;
    }

    public static function decryptWithRSA15(RSAKey $key, $c)
    {
        if (mb_strlen($c, '8bit') !== $key->getModulusLength()) { // or if k < 11
            return false;
        }

        $c = BigInteger::createFromBinaryString($c);
        $m = self::getRSADP($key, $c);
        $em = self::convertIntegerToOctetString($m, $key->getModulusLength());
        if ($em === false) {
            return false;
        }

        if (ord($em[0]) != 0 || ord($em[1]) > 2) {
            return false;
        }

        $ps = mb_substr($em, 2, mb_strpos($em, chr(0), 2, '8bit') - 2, '8bit');
        $m = mb_substr($em, mb_strlen($ps, '8bit') + 3, null, '8bit');

        if (strlen($ps) < 8) {
            return false;
        }

        return $m;
    }

    /**
     * Encryption.
     *
     * @param RSAKey $key
     * @param string $plaintext
     * @param string $hash_algorithm
     *
     * @return string
     */
    public static function encryptWithRSAOAEP(RSAKey $key, string $plaintext, string $hash_algorithm): string
    {
        /** @var Hash $hash */
        $hash = Hash::$hash_algorithm();
        $length = $key->getModulusLength() - 2 * $hash->getLength() - 2;
        if (0 >= $length) {
            throw new \RuntimeException();
        }
        $plaintext = str_split($plaintext, $length);
        $ciphertext = '';
        foreach ($plaintext as $m) {
            $ciphertext .= self::encryptRSAESOAEP($key, $m, $hash);
        }

        return $ciphertext;
    }

    /**
     * Decryption.
     *
     * @param RSAKey $key
     * @param string $ciphertext
     * @param string $hash_algorithm
     *
     * @return string
     */
    public static function decryptWithRSAOAEP(RSAKey $key, string $ciphertext, string $hash_algorithm): string
    {
        if (0 >= $key->getModulusLength()) {
            throw new \RuntimeException();
        }
        $hash = Hash::$hash_algorithm();
        $ciphertext = str_split($ciphertext, $key->getModulusLength());
        $ciphertext[count($ciphertext) - 1] = str_pad($ciphertext[count($ciphertext) - 1], $key->getModulusLength(), chr(0), STR_PAD_LEFT);
        $plaintext = '';
        foreach ($ciphertext as $c) {
            $temp = self::getRSAESOAEP($key, $c, $hash);
            $plaintext .= $temp;
        }

        return $plaintext;
    }

    /**
     * @param BigInteger $x
     * @param int        $xLen
     *
     * @return string
     */
    private static function convertIntegerToOctetString(BigInteger $x, int $xLen): string
    {
        $x = $x->toBytes();
        if (strlen($x) > $xLen) {
            throw new \RuntimeException('Invalid length.');
        }

        return str_pad($x, $xLen, chr(0), STR_PAD_LEFT);
    }

    /**
     * Octet-String-to-Integer primitive.
     *
     * @param string $x
     *
     * @return BigInteger
     */
    private static function convertOctetStringToInteger(string $x): BigInteger
    {
        return BigInteger::createFromBinaryString($x);
    }

    /**
     * Exponentiate with or without Chinese Remainder Theorem.
     * Operation with primes 'p' and 'q' is appox. 2x faster.
     *
     * @param RSAKey     $key
     * @param BigInteger $c
     *
     * @return BigInteger
     */
    private static function exponentiate(RSAKey $key, BigInteger $c): BigInteger
    {
        if ($key->isPublic() || empty($key->getPrimes())) {
            return $c->modPow($key->getExponent(), $key->getModulus());
        }

        $p = $key->getPrimes()[0];
        $q = $key->getPrimes()[1];
        $dP = $key->getExponents()[0];
        $dQ = $key->getExponents()[1];
        $qInv = $key->getCoefficient();

        $m1 = $c->modPow($dP, $p);
        $m2 = $c->modPow($dQ, $q);
        $h = $qInv->multiply($m1->subtract($m2)->add($p))->mod($p);
        $m = $m2->add($h->multiply($q));

        return $m;
    }

    /**
     * RSA EP.
     *
     * @param RSAKey     $key
     * @param BigInteger $m
     *
     * @return BigInteger
     */
    private static function getRSAEP(RSAKey $key, BigInteger $m): BigInteger
    {
        if ($m->compare(BigInteger::createFromDecimal(0)) < 0 || $m->compare($key->getModulus()) > 0) {
            throw new \RuntimeException();
        }

        return self::exponentiate($key, $m);
    }

    /**
     * RSA DP.
     *
     * @param RSAKey     $key
     * @param BigInteger $c
     *
     * @return BigInteger
     */
    private static function getRSADP(RSAKey $key, BigInteger $c): BigInteger
    {
        if ($c->compare(BigInteger::createFromDecimal(0)) < 0 || $c->compare($key->getModulus()) > 0) {
            throw new \RuntimeException();
        }

        return self::exponentiate($key, $c);
    }

    /**
     * MGF1.
     *
     * @param string $mgfSeed
     * @param int    $maskLen
     * @param Hash   $mgfHash
     *
     * @return string
     */
    private static function getMGF1(string $mgfSeed, int $maskLen, Hash $mgfHash): string
    {
        $t = '';
        $count = ceil($maskLen / $mgfHash->getLength());
        for ($i = 0; $i < $count; ++$i) {
            $c = pack('N', $i);
            $t .= $mgfHash->hash($mgfSeed.$c);
        }

        return mb_substr($t, 0, $maskLen, '8bit');
    }

    /**
     * RSAES-OAEP-ENCRYPT.
     *
     * @param RSAKey $key
     * @param string $m
     * @param Hash   $hash
     *
     * @return string
     */
    private static function encryptRSAESOAEP(RSAKey $key, string $m, Hash $hash): string
    {
        $mLen = mb_strlen($m, '8bit');
        $lHash = $hash->hash('');
        $ps = str_repeat(chr(0), $key->getModulusLength() - $mLen - 2 * $hash->getLength() - 2);
        $db = $lHash.$ps.chr(1).$m;
        $seed = random_bytes($hash->getLength());
        $dbMask = self::getMGF1($seed, $key->getModulusLength() - $hash->getLength() - 1, $hash/*MGF*/);
        $maskedDB = strval($db ^ $dbMask);
        $seedMask = self::getMGF1($maskedDB, $hash->getLength(), $hash/*MGF*/);
        $maskedSeed = $seed ^ $seedMask;
        $em = chr(0).$maskedSeed.$maskedDB;

        $m = self::convertOctetStringToInteger($em);
        $c = self::getRSAEP($key, $m);
        $c = self::convertIntegerToOctetString($c, $key->getModulusLength());

        return $c;
    }

    /**
     * RSAES-OAEP-DECRYPT.
     *
     * @param RSAKey $key
     * @param string $c
     * @param Hash   $hash
     *
     * @return string
     */
    private static function getRSAESOAEP(RSAKey $key, string $c, Hash $hash): string
    {
        $c = self::convertOctetStringToInteger($c);
        $m = self::getRSADP($key, $c);
        $em = self::convertIntegerToOctetString($m, $key->getModulusLength());
        $lHash = $hash->hash('');
        $maskedSeed = mb_substr($em, 1, $hash->getLength(), '8bit');
        $maskedDB = mb_substr($em, $hash->getLength() + 1, null, '8bit');
        $seedMask = self::getMGF1($maskedDB, $hash->getLength(), $hash/*MGF*/);
        $seed = strval($maskedSeed ^ $seedMask);
        $dbMask = self::getMGF1($seed, $key->getModulusLength() - $hash->getLength() - 1, $hash/*MGF*/);
        $db = $maskedDB ^ $dbMask;
        $lHash2 = mb_substr($db, 0, $hash->getLength(), '8bit');
        $m = mb_substr($db, $hash->getLength(), null, '8bit');
        if (!hash_equals($lHash, $lHash2)) {
            throw new \RuntimeException();
        }
        $m = ltrim($m, chr(0));
        if (1 !== ord($m[0])) {
            throw new \RuntimeException();
        }

        return mb_substr($m, 1, null, '8bit');
    }
}