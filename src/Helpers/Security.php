<?php

namespace Fjarfs\SrcService\Helpers;

class Security
{
    /**
     * Hash
     *
     * @return string
     */
    private static function hash()
    {
        return 'sha256';
    }

    /**
     * Encryption key
     *
     * @return string
     */
    private static function key()
    {
        return config('app.key');
    }

    /**
     * Encryption cipher
     *
     * @return string
     */
    private static function cipher()
    {
        return config('app.cipher');
    }

    /**
     * Encrypt
     *
     * @param string $value
     * @return string
     */
    public static function encrypt($value)
    {
        $key   = hash(self::hash(), self::key());
        $iv    = substr($key, 0, openssl_cipher_iv_length(self::cipher()));

        $value = serialize($value);
        $value = openssl_encrypt($value, self::cipher(), $key, 0, $iv);
        $value = base64_encode($value);

        return $value;
    }

    /**
     * Decrypt
     *
     * @param string $value
     * @return string
     */
    public static function decrypt($value)
    {
        $key   = hash(self::hash(), self::key());
        $iv    = substr($key, 0, openssl_cipher_iv_length(self::cipher()));

        $value = base64_decode($value);
        $value = openssl_decrypt($value, self::cipher(), $key, 0, $iv);
        $value = unserialize($value);

        return $value;
    }
}
