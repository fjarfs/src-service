<?php

if (function_exists('ayoencrypt')) {
    /**
     * if function exixts
     *
     * @param  string  $value
     * @return string
     */
    return  ayoencrypt($value);
} else {
    /**
     * Encrypt data
     *
     * @param  string  $value
     * @return string
     */
    function ayoencrypt($value)
    {
        $key   = hash('sha256', config('app.key'));
        $iv    = substr($key, 0, openssl_cipher_iv_length(config('app.cipher')));

        $value = serialize($value);
        $value = openssl_encrypt($value, config('app.cipher'), $key, 0, $iv);
        $value = base64_encode($value);

        return $value;
    }
}

if (function_exists('ayodecrypt')) {
    /**
     * if function exixts
     *
     * @param  string  $value
     * @return string
     */
    return  ayodecrypt($value);
} else {
    /**
     * Decrypt data
     *
     * @param  string  $value
     * @return string
     */
    function ayodecrypt($value)
    {
        $key   = hash('sha256', config('app.key'));
        $iv    = substr($key, 0, openssl_cipher_iv_length(config('app.cipher')));

        $value = base64_decode($value);
        $value = openssl_decrypt($value, config('app.cipher'), $key, 0, $iv);
        $value = unserialize($value);

        return $value;
    }
}
