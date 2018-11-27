<?php

namespace Handscube\Assistants;

use Handscube\Traits\AppAccesserTrait;

/**
 * Class Encrypt [c] Handscube
 * @author J.W. <email@email.com>
 */

class Encrypt
{
    use AppAccesserTrait;

    public static function hash(string $algo, string $data, bool $rawOutput = false)
    {
        return hash($algo, $data, $rawOutput);
    }

    private static function encode(string $value, string $key = "")
    {
        $key = $key ?: environment()["APP_KEY"] ?: Config::get("app_key") ?: self::$appKey ?: null;
        if (!key) {
            throw new InsideException("APP_KEY must be given in cookie operation.");
        }
        $ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
        $encryptValue = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $value, MCRYPT_MODE_ECB, $iv);
        return trim(base64_encode($encryptValue));
    }

    public static function decode($value)
    {
        $key = $key ?: environment()["APP_KEY"] ?: Config::get("app_key") ?: self::$appKey ?: null;
        $cryptValue = base64_decode($value);
        $ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
        $decryptValue = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $cryptValue, MCRYPT_MODE_ECB, $iv);
        return trim($decryptValue);
    }

    public static function signAppKey()
    {
        $body = 'app_key' . '+' . time();
        $body64 = base64_encode(urlencode($body));
        return base64_encode(mt_rand(100, 1000)) . '.' . $body64 . '==';
    }
}
