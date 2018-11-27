<?php

namespace Handscube\Assistants;

use Handscube\Abstracts\Features\StaticRewriteAccesser;
use Handscube\Traits\AppAccesserTrait;

/**
 * Assistant class [c] Handscube.
 *
 * @author J.W.
 */

class Assistant implements StaticRewriteAccesser
{

    const type = "Assistant";

    use AppAccesserTrait;

    public function __construct()
    {

    }

    // public static function __callStatic($name, $args)
    // {
    //     $classAccesser = static::apply();
    //     $applyInstance = self::make($classAccesser);
    //     if (!method_exists($applyInstance, $name)) {
    //         throw new NotFoundException("Method $name not found in $classAccesser!");
    //     }
    //     return $applyInstance->{$name}($args);
    // }

    public static function __callStatic($funcName, $funcArgs)
    {

    }

}
