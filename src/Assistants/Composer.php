<?php

namespace Handscube\Assistants;

use Handscube\Abstracts\Features\FastcallAble;

class Composer extends Assistant implements FastcallAble
{

    public function __construct()
    {

    }

    public static function apply()
    {
        return __CLASS__;
    }

    function use ($path, $isOnce = true) {
        return self::import($path, true, $isOnce);
    }

    public static function import(string $path, bool $shouldReturn = false, $isOnce = true)
    {
        if ($shouldReturn) {
            return $isOnce ? require_once $path : require $path;
        }
        if ($isOnce) {
            require_once $path;
        }
        require $path;
    }
}
