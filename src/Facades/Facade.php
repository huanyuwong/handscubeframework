<?php

namespace Handscube\Facades;

class Facade extends \Handscube\Kernel\Component
{

    public static function __callStatic($name, $arguments)
    {
        return (self::app()->make(static::apply(), false))->$name(...$arguments);
    }
}
