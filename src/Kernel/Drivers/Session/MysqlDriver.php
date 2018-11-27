<?php

namespace Handscube\Kernel\Drivers\Session;

use Handscube\Abstracts\Features\SessionDriverAble;

/**
 * Mysql Session Driver. [c] Handscube
 * @author J.W. <email@email.com>
 *
 * == TABLE ==
 * id | varchar
 * expire | datetime
 * data | text
 * ===========
 */

class MysqlDriver implements SessionDriverAble
{
    // use AppAccesserTrait;
    private $link;
    private $expire = '1 hour';

    public function __construct()
    {

    }

    public function open($savePath, $sessionName)
    {
        $dbConfig = config('db');
        $dsn = 'mysql:dbname=' . $dbConfig['mysql']['database'] . ';' . 'host=' . $dbConfig['mysql']['host'];
        $user = $dbConfig['mysql']['username'];
        $password = $dbConfig['mysql']['password'];
        try {
            $this->link = new \PDO($dsn, $user, $password);
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }
    public function close()
    {
        unset($this->link);
        return true;
    }
    public function read($id)
    {
        $sth = $this->link->prepare("select data
        from session
        where id = ? and expire > ?");
        $sth->execute([$id, date('Y-m-d H:i:s')]);
        if ($row = $sth->fetchAll()) {
            return $row[0];
        } else {
            return '';
        }
    }

    public function write($id, $data)
    {
        $time = date('Y-m-d H:i:s');
        $expireTime = date('Y-m-d H:i:s', strtotime($time . '+' . $this->expire));
        $sth = $this->link->prepare('replace
        into session
        set id = ?, expire = ?, data = ?');
        $res = $sth->execute([$id, $expireTime, $data]);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }
    public function destroy($id)
    {
        $sth = $this->link->prepare('delete from session where id = ?');
        $res = $sth->execute([$id]);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }
    public function gc($maxlifetime)
    {
        $currentTime = time();
        $sth = $this->link->prepare('delete from
        session
        where expire <= ?');
        $result = $sth->execute([$currentTime]);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}
