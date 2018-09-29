<?php

namespace Handscube\Traits;

trait FastcallTrait {

    public static function __callStatic($funcName, $funcArgs){
        return static::apply();
    }
}