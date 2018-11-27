<?php

namespace Handscube\Foundations;

use Handscube\Abstracts\Features\AppAccesser;
use Handscube\Abstracts\Features\StaticRewriteAccesser;
use Handscube\Abstracts\Features\UpstreamAble;
use Handscube\Handscube;
use Handscube\Kernel\Exceptions\KernelException;

/**
 * Base Component class . #Handscube framework.
 */

abstract class BaseComponent implements AppAccesser, UpstreamAble, StaticRewriteAccesser
{

    const type = "Component";

    public function beforeGetter($property)
    {

    }

    /**
     * return a upstream class, so this instance can access in your class.
     */
    public static function upstream()
    {
        return \Handscube\Handscube::class;
    }

    public function __get($property)
    {
        if ($this->beforeGetter($property)) {
            return $this->beforeGetter($property);
        }
        $getter = "get" . ucfirst(strtolower($property));
        if (method_exists($this, $getter)) {
            return call_user_func([$this, $getter]);
        }

    }

    public function __set($property, $value)
    {
        $setter = "set" . ucfirst(strtolower($property));
        if (method_exists($this, $setter)) {
            return call_user_func([$this, $setter], $property, $value);
        } else {
            return 0;
        }

    }

    public static function __callStatic($fnName, $fnArgs)
    {

    }

    public function getApp()
    {
        if (!self::upstream()::$app) {
            throw new KernelException("The Application cannot be accessed because initialization is not completed.");
        }
        return self::upstream()::$app;
    }

    public static function app()
    {
        return Handscube::$app;
    }

}
