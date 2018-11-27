<?php

namespace Handscube\Kernel;

use Handscube\Foundations\Injector;
use Handscube\Traits\AppAccesserTrait;

class ModelInjector extends Injector
{
    use AppAccesserTrait;

    public static function inject($class, $index)
    {
        if (is_array($index)) {

        }
        // return $class::class::find($index);
    }
}
