<?php

namespace Handscube\Foundations;

abstract class Injector
{

    abstract public static function inject($class, $index);

}
