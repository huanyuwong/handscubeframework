<?php

namespace Handscube\Assistants;

use Handscube\Abstracts\Features\FastcallAble;

/**
 * Cookie class [c] Handscube.
 *
 * @author J.W.
 */
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

    /**
     * Cookie constructor.
     *
     * @param [type] $name
     * @param [type] $value
     * @param integer $expire
     * @param string $path
     * @param string $domain
     * @param boolean $secure
     * @param boolean $httpOnly
     */
    public function __construct($name, $value, $expire = 0, $path = '/', $domain = '', $secure = false, $httpOnly = false)
    {
        $this->name($name)
            ->value($value)
            ->expire($expire)
            ->path($path)
            ->domain($domain)
            ->secure($secure)
            ->httpOnly($httpOnly);
    }

    /**
     * Set cookie name
     *
     * @param [type] $cookieName
     * @return Object $this
     */
    public function name($cookieName)
    {
        $this->name = $cookieName;
        return $this;
    }

    /**
     * Set cookie value
     *
     * @param [type] $cookieValue
     * @return Object $this
     */
    public function value($cookieValue)
    {
        $this->value = self::encrypt($cookieValue);
        return $this;
    }

    /**
     * Set cookie expire
     *
     * @param integer $expireTime
     * @return void
     */
    public function expire($expireTime = 0)
    {
        $this->expire = $expireTime;
        return $this;
    }

    /**
     * Set cookie path
     *
     * @param string $path
     * @return void
     */
    public function path($path = "/")
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Set domain.
     *
     * @param string $domain
     * @return void
     */
    public function domain($domain = "")
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * Set whether open sucure mode.
     *
     * @param boolean $secure
     * @return void
     */
    public function secure($secure = false)
    {
        $this->secure = $secure;
        return $this;
    }

    /**
     * Set whether http only.
     *
     * @param boolean $httpOnly
     * @return void
     */
    public function httpOnly($httpOnly = false)
    {
        $this->httpOnly = $httpOnly;
        return $this;
    }

    /**
     * Save cookie.
     *
     * @return void
     */
    public function save()
    {
        return setcookie($this->name, $this->value, $this->expire, $this->path, $this->domain, $this->secure, $this->httpOnly);
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
        if (isset($_COOKIE[$name])) {
            return false;
        }
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
        if (isset($_COOKIE[$name])) {
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
    private static function encrypt($info, $key = '')
    {
        $key = $key ?: environment()["APP_KEY"] ?: Config::get("app_key") ?: self::$appKey ?: null;
        if (!$key) {
            throw new InsideException("APP_KEY must be given in cookie operation.");
        }
        // srand((double) microtime() * 10);
        $encrypt_key = md5(rand(0, 10000));
        $ctr = 0;
        $tmp = '';
        for ($i = 0; $i < strlen($info); $i++) {
            $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
            $tmp .= $encrypt_key[$ctr] . ($info[$i] ^ $encrypt_key[$ctr++]);
        }
        return base64_encode(self::passport($tmp, $key));
    }

    /**
     * Decrypt cookie.
     *
     * @param [type] $info
     * @param string $key
     * @return void
     */
    private static function decrypt($info, $key = '')
    {
        $key = $key ?: environment()["APP_KEY"] ?: Config::get("app_key") ?: self::$appKey ?: null;
        if (!$key) {
            throw new InsideException("APP_KEY must be given in cookie operation.");
        }
        $info = self::passport(base64_decode($info), $key);
        $tmp = '';
        for ($i = 0; $i < strlen($info); $i++) {
            $md5 = $info[$i];
            $tmp .= $info[++$i] ^ $md5;
        }
        return $tmp;
    }

    /**
     * Passport.
     *
     * @param [type] $info
     * @param [type] $encrypt_key
     * @return void
     */
    private static function passport($info, $encrypt_key)
    {
        $encrypt_key = md5($encrypt_key);
        $ctr = 0;
        $passport = '';
        for ($i = 0; $i < strlen($info); $i++) {
            $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
            $passport .= $info[$i] ^ $encrypt_key[$ctr++];
        }
        return $passport;
    }

    /**
     * Decode cookie value.
     *
     * @param [type] $value
     * @return void
     */
    // private static function decrypt($value)
    // {
    //     $key = $key ?: environment()["APP_KEY"] ?: Config::get("app_key") ?: self::$appKey ?: null;
    //     $cryptValue = base64_decode($value);
    //     $ivSize = \mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
    //     $iv = \mcrypt_create_iv($ivSize, MCRYPT_RAND);
    //     $decryptValue = \mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $cryptValue, MCRYPT_MODE_ECB, $iv);
    //     return trim($decryptValue);
    // }

    public static function all()
    {
        return $_COOKIE;
    }

    public static function apply()
    {

    }

}
