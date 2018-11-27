<?php

namespace Handscube\Traits;

use Handscube\Kernel\Exceptions\InsideException;
use Handscube\Kernel\Exceptions\NotFoundException;
use Handscube\Kernel\Request;

/**
 * Resolve Trait [c] Handscube.
 * This trait must be depend on Application
 *
 * @author J.W.
 */

trait ResolveTrait
{

    public $excludeIoc = []; //The classes which not be injected by resolveDepends.

    /**
     * @param [mixed] $class
     * @param [string] $method
     * @param [array] $params
     */
    public function call($class, $method, $params = [])
    {
        return $this->resolveMethod($class, $method, $params);
    }

    /**
     * Create a instance class you need.
     *
     * @param [string] $class [class name]
     * @param [boolean] $isLoadDepends [Deciding whether to instantiate an object inculding its depends.]
     */
    public function make($class, $isLoadDepends = true)
    {

        // if there is an instance in the $instacneMap, return it at first.
        if ($isLoadDepends === false) {
            return new $class;
        }
        $refClass = new \ReflectionClass($class);
        if (!$refClass->hasMethod("__construct") || !$refClass->getMethod("__construct")->getParameters()) {
            return new $class;
        }
        return $this->resolve($class);
    }

    /**
     * Resolave object
     * Returns an object that contains a dependency
     * @param [Object] $class
     * @param [Bool] $shouldDepend
     * @return [Object instance]
     */
    public function resolve($class, $shouldDepend = true)
    {
        if (!class_exists($class)) {
            throw new NotFoundException("Class $class is not found.");
        }

        if (!$shouldDepend) {
            return $this->isSingleton($class) ? $this->componentsMap[$class] : new $class;
        } else {
            if ($this->isSingleton($class)) {
                return $this->componentsMap[$class];
            }
            $classDepends = $this->resolveDepends($class);
            $instance = (new \ReflectionClass($class))->newInstanceArgs($classDepends);
            return $instance;
        }
    }

    /**
     * Call the method in IoC way.
     * @param [mixed] $className [String or an object]
     * @param [type] $methodName []
     * @param [Array] $exParams  []
     * @return [type] $mixed [method return]
     *
     */
    public function resolveMethod($class = __CLASS__, $method, $exParams = [])
    {
        if (is_string($class)) {
            $instance = $this->resolve($class);
        } else if (is_object($class)) {
            $instance = $class;
            $class = get_class($class);
        } else {
            throw new InsideException("Parameter \$class can only be a string or a object.");
        }
        $paramArr = $this->resolveMethodDepends($class, $method);
        // $instance->{$method}();
        // exit();
        return $instance->{$method}(...array_merge($paramArr, $exParams));
    }

    /**
     * @param [String] $class [full class name Handscube\Kernel\Request]
     * @param [String] $method [method name]
     * @return [Array] $depends [depends of a object or a method]
     *
     * Get depends of a object or a method.
     */
    public function resolveDepends(string $class, $method = '__construct')
    {
        if (!class_exists($class)) {
            throw new NotFoundException("Class $class is not found.");
        }
        $refClass = new \ReflectionClass($class);
        $depends = [];
        if (!$refClass->hasMethod($method) && $method === "__construct") { //The situation where the constructor does not exist.
            return new $class;
        }
        if ($refClass->hasMethod($method)) {
            $method = $refClass->getMethod($method);
            $dependsParams = $method->getParameters();
            if (count($dependsParams) > 0) {
                foreach ($dependsParams as $param) {
                    if ($class = $param->getClass()) {
                        $className = $class->getName();
                        $prevDepend = $this->resolveDepends($className);
                        //handle the depend object which do not have __construct function or a singleton instance.
                        if (is_object($prevDepend)) {
                            $depends[] = $prevDepend;
                            continue;
                        }
                        if ($this->isSingleton($className)) {
                            $depends[] = $this->getSingleton($className);
                        } else {
                            //$className::type === "Model" && $this->isControllerCall
                            if ($className::type === "Model" && $this->isControllerCall) {
                                $model = $this->getActionBoundModel($className);
                                $depends[] = $model;
                            } else {
                                $depends[] = (new \ReflectionClass($className))->newInstanceArgs($prevDepend);
                            }
                        }
                    }
                }
            }
        }
        return $depends; //return finally depends.
    }

    /**
     * @param [String] $className []
     * @param [String] $method []
     * @return [Array] $depends []
     *
     * Resolve class method depends.
     */
    public function resolveMethodDepends($className = __CLASS__, $method)
    {
        // ff($this->resolveDepends($className, $method));
        return $this->resolveDepends($className, $method);
    }

    /**
     * Check class is model or not.
     *
     * @param string $modelName
     * @return boolean
     */
    public function isModel(string $modelName)
    {
        return $modelName::type === "Model" ? true : false;
    }
}
