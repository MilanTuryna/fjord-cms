<?php


namespace App\Model;


use Exception;

class Cryptography
{
    /**
     * @throws Exception
     */
    public static function createUnique($len = 13): string {
        // used solution on: https://www.php.net/manual/en/function.uniqid.php#120123
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($len / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($len / 2));
        } else {
            throw new Exception("no cryptographically secure random function available");
        }
        return substr(bin2hex($bytes), 0, $len);
    }
}