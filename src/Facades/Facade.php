<?php

namespace Handscube\Facades;

use Handscube\Handscube;

/**
 * Class Facade [c] Handscube.
 * @author J.W. <email@email.com>
 */

class Facade extends \Handscube\Kernel\Component
{

    public static $resolveEntites = [];

    public static function __callStatic($name, $arguments)
    {
        // f($name);
        // f($arguments);exit();
        return self::resolveEntity($name, $arguments);
    }

    protected static function resolveEntity($name, $args)
    {
        if (is_object(static::apply())) {
            return (static::apply())->$name(...$args);
        }
        if (isset(static::$resolveEntites[static::apply()])) {
            return (static::$resolveEntites[static::apply()])->$name(...$args);
        }
        return (self::app()->make(static::apply(), false))->$name(...$args);

    }
}
