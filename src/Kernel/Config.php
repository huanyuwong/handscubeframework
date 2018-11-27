<?php

namespace Handscube\Kernel;

use Handscube\Traits\AppAccesserTrait;

class Config
{

    use AppAccesserTrait;

    public static function get($key, $file = "app")
    {
        if ($file === "app") {
            return self::app()->appConfig($key);
        }

        if ($file === "database") {
            return self::app()->databaseConfig($key);
        }

        return 0;
    }

}
