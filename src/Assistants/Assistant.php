<?php

namespace Handscube\Assistants;

use Handscube\Abstracts\Features\StaticRewriteAccesser;
use Handscube\Traits\ResolveTrait;
use Handscube\Kernel\Exceptions\NotFoundException;

class Assistant implements StaticRewriteAccesser{

    use ResolveTrait;

    public function __construct()
    {
        
    }

    public static function __callStatic($name, $args) {
        $classAccesser = static::apply();
        $applyInstance = self::make($classAccesser);
        if(!method_exists($applyInstance, $name)){
            throw new NotFoundException("Method $name not found in $classAccesser!");
        }
        return $applyInstance->{$name}($args);
    }

}