<?php

namespace Handscube\Assistants;

use Handscube\Abstracts\Features\FastcallAble;
use Handscube\Kernel\Config;
use Handscube\Kernel\Exceptions\InsideException;

class Cookie extends Assistant implements FastcallAble
{

    protected $name;
    protected $value;
    protected $expireTime = 0;
    protected $path = "/";
    protected $domain = "";
    protected $secure = false;
    protected $httpOnly = false;

    private static $appKey = "9ffasdfw34jnasdfwr=sdfn2e";

    public function name($cookieName)
    {
        $this->name = $cookieName;
        return $this;
    }

    public function value($cookieValue)
    {
        $this->value = self::encrypt($cookieValue);
        return $this;
    }

    public function expire($expireTime = 0)
    {
        $this->expire = $expireTime;
        return $this;
    }

    public function path($path = "/")
    {
        $this->path = $path;
        return $this;
    }

    public function domain($domain = "")
    {
        $this->domain = $domain;
        return $this;
    }

    public function secure($secure = false)
    {
        $this->secure = $secure;
        return $this;
    }

    public function httpOnly($httpOnly = false)
    {
        $this->httpOnly = $httpOnly;
        return $this;
    }

    public function save()
    {
        setcookie($this->name, $this->value, $this->expire, $this->path, $this->domain, $this->secure, $this->httpOnly);
    }

    /**
     * Set cookie value
     *
     * @param [type] $name
     * @param [type] $value
     * @param integer $expire
     * @param string $path
     * @param string $domain
     * @param boolean $secure
     * @param boolean $httpOnly
     * @return [string] value
     */
    public static function set($name, $value, $expire = 0, $path = "/", $domain = "", $secure = false, $httpOnly = false)
    {
        return setcookie($name, self::encrypt($value), $expire, $path, $domain, $secure, $httpOnly);
    }

    /**
     * Get cookie value by name.
     *
     * @param [type] $name
     * @return void
     */
    public static function get($name)
    {
        if ($_COOKIE[$name]) {
            return self::decrypt($_COOKIE[$name]);
        }
        return null;
    }

    /**
     * Encrypt cookie value.
     *
     * @param [type] $value
     * @param string $key
     * @return void
     */
    private static function encrypt($value, $key = "")
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

    /**
     * Decode cookie value.
     *
     * @param [type] $value
     * @return void
     */
    private static function decrypt($value)
    {
        $key = $key ?: environment()["APP_KEY"] ?: Config::get("app_key") ?: self::$appKey ?: null;
        $cryptValue = base64_decode($value);
        $ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
        $decryptValue = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $cryptValue, MCRYPT_MODE_ECB, $iv);
        return trim($decryptValue);
    }

    public static function apply()
    {

    }

}
