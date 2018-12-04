<?php

namespace Handscube\Facades;

use Handscube\Kernel\DBConnection;

class DB extends Facade
{
    public static function apply()
    {
        return DBConnection::exportDB();
    }
}
