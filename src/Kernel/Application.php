<?php

namespace Handscube\Kernel;

use Handscube\Abstracts\App;
use Handscube\Assistants\Cookie;

class Application extends App {

    use \Handscube\Traits\ResolveTrait;

    private $path;
    private $ctrlPath;
    protected $cube;

    protected static $instanceMap = [];

    const __CTRL_NAMESPACE__ = "App\\Controllers\\";

    function __construct($path)
    {
        $this->path = $path;
        $this->ctrlPath = $this->path . "Controllers/";
    }


    public function instanceCube(){
        
    }

    public function handle($request) {

    }

    public static function test(){
        Cookie::find();
    }

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
		//Loading class that contain dependencies.
        $instance = self::resolve($class);
		self::$instanceMap[$class] = $instance;
		return $instance;
    }
    
    static function singleton($class,$isLoadDepends = true){
        if (self::$instanceMap[$class]) {
			return self::$instanceMap[$class];
        }
        return self::make($class,$isLoadDepends);
    }


    public function setExcludeClass(string $class){

    }

    public function getExculdeClasses(){
        return self::$excludeIoc;
    }


    public function getInstanceMap(){
        return self::$instacneMap;
    }

    public function load($ctrlName){
        require_once $this->ctrlPath . $ctrlName . ".php";
    }
    
    public function getPath(){
        return $this->path;
    }

    public function getCtrlPath(){
        return $this->ctrlPath;
    }

    public function isClassExist(){

    }
}