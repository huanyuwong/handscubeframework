<?php

namespace Handscube\Kernel\Drivers\Session;

use Handscube\Abstracts\Features\SessionDriverAble;
use Handscube\Kernel\Redis;

/**
 * Redis Session Driver. [c] Handscube
 * @author J.W. <email@email.com>
 */

class RedisDriver implements SessionDriverAble
{
    // use AppAccesserTrait;
    private $link;
    private $expire = 30;
    private $hashTable = 'session';
    private $driver;

    public function __construct()
    {

    }

    public function open($savePath, $sessionName)
    {
        $this->hashTable = environment()['SESSION_DRIVER_TABLE']
        ?: (config()['session_driver_table'] ?: null);
        $this->driver = new Redis();
        if ($this->driver && $this->hashTable) {
            return true;
        } else {
            return false;
        }
    }

    public function close()
    {
        unset($this->driver);
        return true;
    }

    public function read($id)
    {
        if ($res = $this->driver->hget($this->hashTable, $id)) {
            return unserialize($res);
        }
        return '';
    }

    public function write($id, $data)
    {
        if ($this->driver->hset($this->hashTable, $id, serialize($data))) {
            return true;
        }
        return false;
    }
    public function destroy($id)
    {
        return $this->driver->hdel($this->hashTable, $id) ? true : false;

    }
    public function gc($maxlifetime)
    {
        return true;
    }

    // public function __destruct()
    // {
    //     session_write_close();
    // }
}
