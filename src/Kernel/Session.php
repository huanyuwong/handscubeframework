<?php

namespace Handscube\Kernel;

use Handscube\Kernel\Exceptions\InvalidException;

/**
 * Session class [c] Handscube.
 *
 * author J.W.
 */
class Session extends Component
{

    protected $expire = 2592000;

    public static $start = false;

    /**
     * Start session.
     *
     * @return void
     */
    public static function start()
    {
        if (self::$start === true) {
            // echo "started.\n";g
            return;
        }
        $expire = environment()["SESSION_EXPIRE"] ?: self::$expire;
        self::expire($expire);
        session_name("HANDSCUBE_ID");
        session_start();
        self::$start = true;
    }

    /**
     * Session expire time by cookie.
     *
     * @param [type] $lifetime
     * @param string $path
     * @param string $domain
     * @param boolean $secure
     * @param boolean $httpOnly
     * @return void
     */
    public function expire($lifetime, $path = "/", $domain = "", $secure = false, $httpOnly = false)
    {
        // setcookie(session_name(), session_id(), time() + $lifetime, $path, $domain, $secure, $httpOnly);
        return session_set_cookie_params($lifetime, $path, $domain, $secure, $httpOnly);
    }

    /**
     * Set session name;
     * if values is an object or an array. serialize it at frist.
     * also you can set $serialize with true to force the function serialize the session value.
     *
     * @param [type] $name [session name]
     * @param [type] $value
     * @return void
     */
    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     *  $data : ['name1' => 'filed1', 'name2' => ['filed2-1','filed2-2']];
     *
     * @param array $data
     * @return void
     */
    public function mset(array $data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $filed) {
                $this->set($key, $filed);
            }
        }
    }

    /**
     * Get session value.
     * if value had serialized ,unserialize it at first.
     * @param [type] $name
     * @return void
     */
    public function get($name)
    {
        // if (isset($_SESSION[$name])) {
        //     if ($value = @self::unserialize($name)) {
        //         return $_SESSION[$name];
        //     }
        //     return $_SESSION[$name];
        // } else {
        //     return false;
        // }
        if ($_SESSION[$name]) {
            return $_SESSION[$name];
        }
        return null;
    }

    /**
     * Mulity get session name.
     *
     * @param array $fileds
     * @return void success - array | fail - throw exception
     */
    public function mget(array $fileds)
    {
        $res = [];
        if (is_array($fileds)) {
            foreach ($fileds as $filed) {
                $res[] = $this->get($filed);
            }
            return $res;
        }
        throw new InvalidException("Parameter must be an array.");
    }

    /**
     * unset session name;
     *
     * @param [type] $name
     * @return void
     */
    public function del($name)
    {
        unset($_SESSION[$name]);
    }

    /**
     * Destory all session datas
     *
     * @return void
     */
    public function destroy()
    {
        session_destroy();
    }

    /**
     * Serialisze session value by session name
     * this will serialize like this login_ok|b:1;nome|s:4:"sica";inteiro|i:34;
     * @param [type] $sessionName
     * @param boolean $pure
     * @return void
     */
    public function serialize($value, $pure = false)
    {
        if ($pure) {
            return serialize($value);
        }
        ff(session_encode());
        return session_encode($value);
    }

    /**
     * Unserialize session value.
     *
     * @param [type] $sesionName
     * @param boolean $pure
     * @return void
     */
    public function unserialize($sessionName, $pure = false)
    {
        if ($pure) {
            return unserialize($_SESSION[$sessionName]);
        }
        return session_decode($_SESSION[$sessionName]);
    }

    /**
     * Call GC program immediately.
     * it is recommended to execute GC periodically for
     * production systems using, e.g., "cron" for
     * UNIX-like systems. Therefore, make sure to disable
     * probability based GC by setting session.gc_probability to 0.
     *
     * @return void
     */
    public function gc()
    {
        return session_gc();
    }

}
