<?php

namespace Handscube\Kernel;

class PDO
{

    public $dsn;

    public $driverType;
    public $host;
    public $port;
    public $username;
    public $password;
    public $database;
    public $options;

    // 'mysql:dbname=testdb;host=127.0.0.1;port=3333';

    public function __construct($config, $username = "", $password = "", $options = [])
    {
        if (is_array($config)) {
            $this->driverType = $config["driver"] ?: "mysql";
            $this->host = $config["host"] ?: "127.0.0.1";
            $this->port = $config["port"] ?: 3306;
            $this->database = $config["database"] ?: "";
            $this->username = $config["username"] ?: "";
            $this->password = $config["password"] ?: "";
            $this->options = $config["options"] ? is_array($config["options"]) ? $config["options"] : [] : [];
            $this->dsn = $this->driverType . ":" . "dbname=$this->database" . ";" . "host=$this->host" . ";" . "port=$this->port";
        }
        if (is_string($config)) {

        }
    }

    public function parseDsnAsArr(string $dsn)
    {

    }
}
