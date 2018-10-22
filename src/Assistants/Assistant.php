<?php

namespace Handscube\Assistants;

use Handscube\Abstracts\Features\StaticRewriteAccesser;
use Handscube\Kernel\Exceptions\NotFoundException;
use Handscube\Traits\ResolveTrait;

class Assistant implements StaticRewriteAccesser
{

    use ResolveTrait;

    public function __construct()
    {

    }

    public static function __callStatic($name, $args)
    {
        echo "Ffffffff=====\n";
        $classAccesser = static::apply();
        $applyInstance = self::make($classAccesser);
        if (!method_exists($applyInstance, $name)) {
            throw new NotFoundException("Method $name not found in $classAccesser!");
        }
        return $applyInstance->{$name}($args);
    }

}
