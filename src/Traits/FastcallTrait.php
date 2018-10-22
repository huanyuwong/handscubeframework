<?php

namespace Handscube\Traits;


/**
 * Use this trait and implement apply() method,
 * you can call non-static method staticly.
 */

trait FastcallTrait {

    public static function __callStatic($funcName, $funcArgs){
        return static::apply();
    }
}