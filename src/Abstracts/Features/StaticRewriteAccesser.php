<?php

namespace Handscube\Abstracts\Features;


interface StaticRewriteAccesser {

    public static function __callStatic($funcName, $funcArgs);
    
}