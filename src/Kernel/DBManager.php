<?php

namespace Handscube\Kernel;

use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;

/**
 * Database connection based on Illuminate database.
 */
class DBManager extends DatabaseManager
{
    protected $PDO;
    protected $connections = [];
    protected $factory;

    public function makeConnection($dbName)
    {
        $config = config('database')['mysql'];
        return $this->factory->make($config, $dbName);
    }

    public function connection($name = 'mysql')
    {
        if (!isset($this->connections[$name])) {
            $this->connections[$name] = $this->configure(
                $this->makeConnection($name), null
            );
        }
        return $this->connections[$name];
    }
}
