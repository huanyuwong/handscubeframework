<?php

namespace Handscube\Traits;

use Handscube\Kernel\Exceptions\NotFoundException;


trait ResolveTrait {


    public static $excludeIoc  = []; //The classes which not be injected by resolveDepends.


	/**
	 * Create a instance class you need.
	 *
	 * @param [string] $class [class name]
	 * @param [boolean] $isLoadDepends [Deciding whether to instantiate an object inculding its depends.]
	 */
	static function make($class, $isLoadDepends = true) {

		// if there is an instance in the $instacneMap, return it at first.
		if ($isLoadDepends === false) {
            return new $class;
		}
        $refClass = new \ReflectionClass($class);
		if (!$refClass->hasMethod("__construct") || !$refClass->getMethod("__construct")->getParameters()) {
			return new $class;
		}
        return self::resolve($class);
    }


	/**
	 * 
	 * Returns an object that contains a dependency
	 */
	public static function resolve($class, $shouldDepend = true){
		if(!class_exists($class)) throw new NotFoundException("Class $class is not found.");
		if(!$shouldDepend) return new $class;
		$classDepends = self::resolveDepends($class);
		// print_r($classDepends);
		// exit();
		$instance = (new \ReflectionClass($class))->newInstanceArgs($classDepends);
		return $instance;
	}


	/**
	 * 
	 * @param [type] $className []
	 * @param [type] $methodName []
	 * @param [Array] $exParams  []
	 * @return [type] $mixed [method return]
	 * 
	 * Call the method in IoC way.
	 */
	public static function resolveMethod($class = __CLASS__, $method, $exParams = []) {
		$instance = self::resolve($class);
		$paramArr = self::resolveMethodDepends($class, $method);
		return $instance->{$method}(...array_merge($paramArr, $exParams));
	}


    /**
	 * @param [String] $class [class name]
	 * @param [String] $method [method name]
	 * @return [Array] $depends [depends of a object or a method]
	 *
	 * Get depends of a object or a method.
	 */
	static function resolveDepends($class, $method = '__construct') {

        if(!class_exists($class)) throw new NotFoundException("Class $class is not found.");
		$refClass = new \ReflectionClass($class);
		$depends = [];
		if (!$refClass->hasMethod($method) && $method === "__construct") {
			return new $class;
		}
		if ($refClass->hasMethod($method)) {
			$method = $refClass->getMethod($method);
			$dependsParams = $method->getParameters();
			if (count($dependsParams) > 0) {
				foreach ($dependsParams as $param) {
					if ($class = $param->getClass()) {
						$className = $class->getName();
                        // if(array_key_exists($className,self::$excludeIoc)) continue; //Exclude the classes contained in self::$excludeIoc.
						$prevDepended = self::resolveDepends($className);
						//handle the depend object which do not have __construct function.
						if (is_object($prevDepended)) {
							$depends[] = $prevDepended;
							continue;
						};
						//handle the normal depend object.
						$depends[] = (new \ReflectionClass($className))->newInstanceArgs($prevDepended);
					}
				}
			}

		};
		return $depends; //return finally depends.
    }
    
    /**
	 * @param [String] $className []
	 * @param [String] $method []
	 * @return [Array] $depends []
     * 
	 * Resolve class method depends.
	 */
	static function resolveMethodDepends($className = __CLASS__, $method) {
		return self::resolveDepends($className, $method);
	}
}