<?php

namespace Handscube\Kernel;

use Handscube\Abstracts\App;
use Handscube\Abstracts\Features\GSAble;
use Handscube\Assistants\Composer;
use Handscube\Handscube;
use Handscube\Kernel\Exceptions\AuthException;
use Handscube\Kernel\Exceptions\InsideException;
use Handscube\Kernel\Exceptions\NotFoundException;
use Handscube\Kernel\Guard;
use Handscube\Kernel\Guards\AppGuard;
use Handscube\Kernel\Request;

/**
 * This is part of Handscube framework.
 *
 * ClassApplication of Handscube framework.
 * @author J.W.
 */
class Application extends App implements GSAble
{

    use \Handscube\Traits\ResolveTrait;
    use \Handscube\Traits\HunterTrait;

    private $path;
    private $ctrlPath;
    private $routePath;
    private $configPath;

    protected $deferComponentsRecord = [];
    protected $componentsMap = [];

    public $componentsNameMap = [];
    public $appConfig = [];
    public $databaseConfig = [];

    public $module;
    public $controller;
    public $action;
    protected $guard;

    const __CTRL_NAMESPACE__ = "App\\Controllers\\";

    public function __construct($path)
    {

        echo "Application __construct\n";
        $this->path = realpath($path);
        $this->ctrlPath = realpath($this->path . "/./controllers/");
        $this->routePath = realpath($this->path . "/../routes/");
        $this->configPath = realpath($this->path . "/../configs/");
        $this->appConfig = Composer::use ($this->configPath . "/App.php");
        $this->databaseConfig = Composer::use ($this->configPath . "/Database.php");
        $this->boot();
        $this->init();

    }

    /**
     * Application boot method.
     * Do things such as reading configuration.
     */
    protected function boot()
    {
        $components = $this->appConfig["components"]["register"];
        if (!$this->registerComponents($components)) { //register components.
            throw new InsideException("Components register fail");
        }
    }

    /**
     * init function
     *
     * @return void
     */
    protected function init()
    {
        $this->registerGuard();
        $db = new Db();
        $db->addConnection($this->databaseConfig["mysql"]);
        $db->bootEloquent();
    }

    /**
     * Register App Gurad.
     *
     * @return void
     */
    public function registerGuard()
    {
        $this->guard = $this->bindGuard() ? $this->make($this->bindGuard(), false) : null;
    }

    /**
     * Bind guard
     *
     * @param string $guardName
     * @return void
     */
    public function bindGuard($guardName = '')
    {
        return $guardName ?: AppGuard::class;
    }

    /**
     * Pass request to guard.
     *
     * @param Guard $guard
     * @param Request $request
     * @return void
     */
    public function handleToGuard(Guard $guard, Request $request)
    {
        $stations = $guard->register();
        if (!$this->checkGuardStations($stations, $request)) {
            exit("Guard check failer!");
        }
    }

    /**
     * Check guard stations.
     *
     * @param array $stations
     * @param Request $request
     * @return void
     */
    public function checkGuardStations(array $stations, Request $request)
    {
        foreach ($stations as $index => $station) {
            if (($this->make($station, false))->runHandle($request) === false) {
                throw new AuthException("App guard {$station} verification failed.");
            }
        }
        return true;
    }

    /**
     * Register a normal component to application so you can access this component
     * in any where like this: $this->app->componentName.
     * @param [Array|String] $components  [componets name]
     * @return [integer] [whether this function register successfy.]
     */
    public function registerComponents($components, $index = "", $shouldLoadDepends = true)
    {

        if (is_array($components) && !empty($components)) {

            foreach ($components as $key => $component) {
                if ($this->componentExists($component)) {
                    if (is_string($key)) {
                        if ($this->singleton($component, $key, $shouldLoadDepends)) {
                            $this->componentsNameMap[$component] = $key;
                        }
                    } else {
                        if ($this->singleton($component, '', $shouldLoadDepends)) {
                            $this->componentsNameMap[$component] = strtolower(\substr($component, strrpos($component, "\\") + 1));
                        }
                    }
                } else {
                    throw new NotFoundException("Component $component is not found.");
                    return 0;
                }
            }
            return 1;
        }
        if (is_string($components) && $this->componentExists($components)) {
            if ($index) {
                $this->singleton($components, $index, $shouldLoadDepends);
            } else {
                $this->singleton($components, '', $shouldLoadDepends);
            }

            return 2;
        }
        return -1;
    }

    /**
     * Handle Request and register it as an app component.
     *
     * @param Request $request
     * @return void
     */
    public function handle(Request $request)
    {

        if ($this->guard) {
            $this->handleToGuard($this->guard, $request);
        }
        // ff($request);
        $this->router->handle($this->request);
    }

    public function guard()
    {
        return AppGuard::class;
    }

    public function test()
    {
        // Cookie::find();
        // print_r(Handscube::$app);
        // $com = new Com();
        // $com->index();
        // $router = new Route();
        // $testControlle = new BaseController();
        // $testControlle->printRequest();

    }

    /**
     * Create a instance class you need.
     *
     * @param [string] $class [class name]
     * @param [boolean] $isLoadDepends [Deciding whether to instantiate an object inculding its depends.]
     */
    // public function make($class, $isLoadDepends = true)
    // {

    //     // if there is an instance in the $instacneMap, return it at first.

    //     if ($isLoadDepends === false) {
    //         return new $class;
    //     }
    //     $refClass = new \ReflectionClass($class);
    //     if (!$refClass->hasMethod("__construct") || !$refClass->getMethod("__construct")->getParameters()) {
    //         return new $class;
    //     }
    //     // echo "application::make\n";exit();
    //     $instance = $this->resolve($class); //Loading class that contain dependencies.
    //     return $instance;
    // }

    /**
     * return a singleton instance and register it as a app component.
     * @param [String] $class [class name]
     * @param [String] $classAlias [class alias name]
     * Use class alias name can access it in component like this $this->app->$classAliasName
     * @return [Object] return object instance you need.
     */
    public function singleton($class, $classAlias = '', $isLoadDepends = true)
    {

        $classKey = $classAlias ? $classAlias : strtolower(\substr($class, strrpos($class, "\\") + 1));

        if (array_key_exists($classKey, $this->componentsMap) && $this->componentsMap[$classKey]) {
            return $this->componentsMap[$classKey];
        }

        $this->componentsMap[$classKey] = $this->make($class, $isLoadDepends);
        $this->componentsNameMap[$class] = $classKey;
        return $this->componentsMap[$classKey];
    }

    /**
     * implement GSAble interface to implement getter setter functionality.
     * e.g.
     * if you access a property that does not exist like this: $this->app->valName
     * the class that implements this interface will call $this->app->getValName() method at first.
     * so the same as set a property.
     * @param [mixed val] $key
     * @return [void]
     */
    public function __get($key)
    {
        if (method_exists($this, "beforeGetter")) {
            if ($this->beforeGetter()) {
                return $this->beforeGetter();
            }
            $this->beforeGetter();
        }
        if (method_exists($this, "get" . ucfirst($key))) {
            return call_user_func([$this, "get" . ucfirst($key)], $key);
        }

        if (method_exists($this, "afterGetter")) {
            return $this->afterGetter($key);
        }

    }

    /**
     * @see __get function annotations above.
     * @param [mixed] $key [property name]
     * @param [mixed] $value [property value]
     * @return [void]
     */
    public function __set($key, $value)
    {
        call_user_func([$this, "set" . ucfirst($key)], $key, $value);
    }

    /**
     * Function can run before the class call a getter or a setter.
     */
    // public function beforeGetter($key){

    // }

    /**
     * Function will run after the class call a getter or a setter.
     */
    public function afterGetter($key)
    {
        return $this->getComponentByKey($key);
    }

    /**
     * Gets the component registered in app.
     * @param [string] $key [property name]
     * @return [Object] [a registered component.]
     */
    public function getComponentByKey(string $key)
    {
        if ($this->getSingletonByKey($key)) {
            return $this->getSingletonByKey($key);
        }
        return $this->registerDeferComponent($key);
    }

    /**
     * Register a defer component when access it.
     *
     * @param [type] $key
     * @return void
     */
    public function registerDeferComponent($key)
    {

        if (!empty($this->appConfig["components"]["defer"])) {
            foreach ($this->appConfig["components"]["defer"] as $idx => $deferComponent) {
                if (is_string($idx)) {
                    if ($key === $idx) {
                        $this->registerComponents($deferComponent, $idx);
                        return $this->getSingletonByKey($key);
                    } else {
                        throw new InsideException("$key is not a index with any registed component.");
                    }

                } else {
                    $deferName = strtolower(\substr($deferComponent, strrpos($deferComponent, "\\") + 1));
                    if ($deferName === $key) {
                        $this->registerComponents($deferComponent, $deferName);
                        return $this->getSingletonByKey($key);
                    } else {
                        throw new InsideException("$key is not a index with any registed component.");
                    }

                }
            }
        } else {
            throw new InsideException("$key is not a index with any registed component.");
        }

    }

    /**
     * Alias with isRegisterComponent() function.
     */
    public function isSingleton($class)
    {
        return array_key_exists($class, $this->componentsNameMap);
    }

    /**
     * Using Full className like "Handscube\Kernel\Request" to find the component is exists or not.
     */
    public function isRegisterComponent($component)
    {
        return $this->isSingleton($component);
    }

    /**
     * Check the component is exists or not . Using key to check $this->componentsMap whether have or not.
     */
    public function isRegsterCompomemtByKey($key)
    {
        return array_key_exists($key, $this->componentsMap);
    }

    /**
     * get Component by full class name.
     */
    public function getSingleton($className)
    {
        if ($this->isSingleton($className)) {
            return $this->componentsMap[$this->componentsNameMap[$className]];
        }
        return 0;
    }

    /**
     * Alis with getSingleton() function.
     */
    public function getComponent($className)
    {
        return $this->getSingleton($className);
    }

    /**
     * get component through index.(alias name)
     *
     * @param [string] $key
     * @return void
     */
    public function getSingletonByKey($key)
    {
        if ($this->isRegsterCompomemtByKey($key)) {
            return $this->componentsMap[$key];
        }
        return 0;
    }

    /**
     * Return a map about all components that registerd in the application.
     *
     * @return array components map.
     */
    public function getComponentsMap()
    {
        return $this->componentsMap;
    }

    /**
     * Get the map that record component alias name.
     *
     * @return array component alias name map.
     */
    public function getComponentsNameMap()
    {
        return $this->componentsNameMap;
    }

    public function otherwise()
    {

    }

    public function setExcludeClass(string $class)
    {

    }

    public function getExculdeClasses()
    {
        return $this->$excludeIoc;
    }

    public function getInstanceMap()
    {
        return $this->$instacneMap;
    }

    public function load($ctrlName)
    {
        require_once $this->ctrlPath . $ctrlName . ".php";
    }

    /**
     * return application path
     *
     * @return string application path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * get controller path.
     *
     * @return string controller path
     */
    public function getCtrlPath()
    {
        return $this->ctrlPath;
    }

    /**
     * get router path
     *
     * @return string route path.
     */
    public function getRoutePath()
    {
        return $this->routePath;
    }

    public function isClassExist()
    {

    }

    public function __destruct()
    {
        unset($this->componentsMap);
        echo "Application __desctruct\n";
    }
}
