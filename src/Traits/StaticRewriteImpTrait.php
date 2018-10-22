<?php

namespace Handscube\Traits;

trait StaticRewriteImpTrait
{
    public static function __callStatic($name, $args)
    {
        $classAccesser = static::apply();
        // $applyInstance = self::make($classAccesser, false);
        $applyInstance = new $classAccesser();
        if (!method_exists($applyInstance, $name)) {
            throw new NotFoundException("Method $name not found in $classAccesser!");
        }
        return $applyInstance->{$name}($args);
    }
}
