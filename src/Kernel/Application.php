<?php

namespace Handscube\Kernel;

use Handscube\Abstracts\App;
use Handscube\Abstracts\Features\GSAble;
use Handscube\Facades\Session;
use Handscube\Handscube;
use Handscube\Kernel\Db;
use Handscube\Kernel\Exceptions\AuthException;
use Handscube\Kernel\Exceptions\InsideException;
use Handscube\Kernel\Exceptions\InvalidException;
use Handscube\Kernel\Exceptions\IOException;
use Handscube\Kernel\Exceptions\NotFoundException;
use Handscube\Kernel\Guard;
use Handscube\Kernel\Guards\AppGuard;
use Handscube\Kernel\Request;
use Handscube\Kernel\Response;
use Handscube\Kernel\Schedules\EventSchedule;
use Handscube\Kernel\Schedules\Schedule;
use Handscube\Kernel\View;

require_once __DIR__ . "//../functions.php";

/**
 * This is part of Handscube framework.
 *
 * Class Application of Handscube framework.
 * @author J.W.
 */
class Application extends App implements GSAble
{

    use \Handscube\Traits\ResolveTrait;
    use \Handscube\Traits\HunterTrait;

    /**
     * App path varibles.
     */
    private $path;
    private $ctrlPath;
    private $routePath;
    private $configPath;

    /**
     * Components variables.
     *
     * @var array
     */
    protected $deferComponentsRecord = [];
    protected $componentsMap = [];
    public $componentsNameMap = [];
    /**
     * Config variables.
     *
     * @var array
     */
    public $appConfig = [];
    public $databaseConfig = [];

    /**
     * Http variables.
     *
     * @var [type]
     */
    public $module;
    public $controller;
    public $action;

    public $isControllerCall = false;

    protected $guard = AppGuard::class;

    private $accessKey;
    private $appKey;
    public $injectsArr;

    //Const.
    const __CTRL_NAMESPACE__ = "App\\Controllers\\";

    /**
     * Constructor.
     *
     * @param [type] $path
     */
    public function __construct($path)
    {
        $this->checkOrigin();
        $this->boot($path);
        $this->init($path);
    }

    /**
     * Application boot method.
     * Do things such as reading configuration,Register Components.
     */
    protected function boot($path)
    {
        $this->ConfigurePath($path);
        if (!$this->registerComponents($this->appConfig["components"]["register"])) {
            throw new InsideException("Components register fail");
        }
    }

    /**
     * Init function.
     * Did some work like init key,registering App Guard and boot ORM.
     * @return void
     */
    protected function init()
    {
        $this->generateKey();
        $this->registerSessionDriver();
        $this->registerGuard();
        $this->registerORM();
        $this->registerNecessaryData();
    }

    /**
     * Register session drivers.
     * [mysql,session]
     *
     * @return void
     */
    protected function registerSessionDriver()
    {
        $driverName = environment()['SESSION_DRIVER'] ?: $this->appConfig['session_driver'];
        if (!$driverName) {
            session_name('HANDSCUBE_ID');
            session_start();
            return;
        }
        $driver = 'Handscube\Kernel\Drivers\Session\\' . ucfirst(strtolower($driverName)) . 'Driver'::class;
        $sessionDriver = new $driver;
        session_set_save_handler($sessionDriver);
        session_start();
    }

    /**
     * Generate app_key and access_key
     *
     * @return void
     */
    public function generateKey()
    {
        $this->createAccessKeyIfNotExists();
        $this->createAppKeyIfNotExists();
    }

    /**
     * Create access key.
     *
     * @return void
     */
    public function createAccessKeyIfNotExists()
    {
        if (!$this->isAccessKeyExists()) {
            $token = CrossGate::signToken();
            if (!setEnv("ACCESS_KEY", $token)) {
                throw new IOException("Set ACCESS_KEY to .env file faild");
            }
        } else {
            return;
        }
    }

    /**
     * Create app key if it not exists.
     *
     * @return void
     */
    public function createAppKeyIfNotExists()
    {
        if (!$this->isAppKeyExists()) {
            $key = \Handscube\Assistants\Encrypt::signAppKey();
            if (!setEnv("APP_KEY", $key)) {
                throw new IOException("Set APP_KEY to .env file faild");
            }
        } else {
            return;
        }
    }

    /**
     * Check app key whether exists or not.
     *
     * @return boolean
     */
    public function isAppKeyExists()
    {
        return getKeyFromEnv("APP_KEY") ? true : false;
    }

    /**
     * Check access key whether exists or not.
     *
     * @return boolean
     */
    public function isAccessKeyExists()
    {
        return getKeyFromEnv("ACCESS_KEY") ? true : false;
    }

    /**
     * Get access key.
     * Used by sign cors access token.
     * @return void
     */
    public function getAccessKey()
    {
        if ($this->accessKey) {
            return $this->accessKey;
        }
        $this->accessKey = getKeyFromEnv('ACCESS_KEY');
        return $this->accessKey;
    }

    /**
     * Get app key.
     * Used by create RFC7519 token and encrypt cookie.
     * @return void
     */
    public function getAppKey()
    {
        return $this->appKey = $this->appKey ?: getKeyFromEnv("APP_KEY");
    }

    /**
     * Check the origin.
     *
     * @return void
     */
    public function checkOrigin()
    {
        $appConfig = config('app');
        $domainConfig = $appConfig["domain_config"];
        \Handscube\Kernel\CrossGate::openCross($domainConfig);
    }

    /**
     * Initialize the path configuration
     *
     * @return void
     */
    public function ConfigurePath($path)
    {
        $this->path = realpath($path);
        $this->ctrlPath = realpath($this->path . "/./controllers/");
        $this->routePath = realpath($this->path . "/../routes/");
        $this->configPath = realpath($this->path . "/../configs/");
        $this->appConfig = require $this->configPath . "/App.php";
        // $this->appConfig = Composer::use ($this->configPath . "/App.php", false);
        // $this->databaseConfig = Composer::use ($this->configPath . "/Database.php", false);
        $this->databaseConfig = require $this->configPath . "/Database.php";
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
     * Bind app guard.
     *
     * @param string $guardName
     * @return void
     */
    public function bindGuard($guardName = '')
    {
        return $this->guard = $guardName ?: AppGuard::class;
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
            if (($this->make($station, false))->handle($request) === false) {
                throw new AuthException("App guard {$station} verification failed.");
            }
        }
        return true;
    }

    /**
     * Boot ORM.
     *
     * @return void
     */
    public function registerORM()
    {
        $db = new Db();
        $db->addConnection($this->databaseConfig["mysql"]);
        $db->bootEloquent();
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
     * Register some necessary data.
     *
     * @return void
     */
    public function registerNecessaryData()
    {
        $this->registerScheduleData();
    }

    /**
     * Register schedule data.
     *
     * @return void
     */
    public function registerScheduleData()
    {
        new EventSchedule(
            \App\Suppliers\ScheduleSupplier::$listeners,
            \App\Suppliers\ScheduleSupplier::$subscribers,
            \App\Suppliers\ScheduleSupplier::$observers
        );
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
        return $this->router->handle($this->request);
    }

    /**
     * Return the application guard.
     *
     * @return void
     */
    public function guard()
    {
        return $this->guard;
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
     * implement GSAble interface to implement getter and setter functionality.
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

    /**
     * Get action bound parameter model.
     *
     * @param [type] $className
     * @return void
     */
    public function getActionBoundModel($className)
    {
        if (Route::$modelsInstance && Route::$modelsAreSort) {
            if ($model = array_shift(Route::$modelsInstance)) {
                array_shift($this->router->paramsWithoutKey);
                return $model;
            } else {
                $id = array_shift($this->router->paramsWithoutKey);
                $model = $this->make($className, false)->find($id);
                if ($model) {
                    return $model;
                }
                throw new InvalidException("Model id $id is invalid");
            }
        }
        $id = array_shift($this->router->paramsWithoutKey);
        $model = $this->make($className, false)->find($id);
        if ($model) {
            return $model;
        }
        throw new InvalidException("Model id $id is invalid");
    }

    /**
     * Send response in differnt type.
     *
     * @param [type] $response
     * @return void
     */
    public function send($response)
    {
        if ($response instanceof Response) {
            $response->send();
        } else if ($response instanceof View) {
            echo $response->getContents();
        } else if (is_string($response)) {
            echo $response;
        } else if (is_array($response) || is_object($response)) {
            print_r($response);
        } else {
            return;
        }
    }

    /**
     * Return a new Handscube\Kernel\Response object.
     *
     * @return void
     */
    public function response()
    {
        return new Response();
    }

    /**
     * Return exculde classes that not contain with Ioc.
     *
     * @return void
     */
    public function getExculdeClasses()
    {
        return $this->$excludeIoc;
    }

    /**
     * Return instance maps.
     *
     * @return void
     */
    public function getInstanceMap()
    {
        return $this->$instacneMap;
    }

    /**
     * Load controller file.
     *
     * @param [type] $ctrlName
     * @return void
     */
    public function load($ctrlName)
    {
        require_once $this->ctrlPath . $ctrlName . ".php";
    }

    /**
     * Return application path
     *
     * @return string application path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get controller path.
     *
     * @return string controller path
     */
    public function getCtrlPath()
    {
        return $this->ctrlPath;
    }

    /**
     * Get router path
     *
     * @return string route path.
     */
    public function getRoutePath()
    {
        return $this->routePath;
    }

    /**
     * Check the file is exists or not.
     *
     * @param [type] $className
     * @return boolean
     */
    public function isClassExist($className)
    {
        return class_exists($className);
    }

    /**
     * Destruct.
     */
    public function __destruct()
    {
        unset($this->componentsMap);
    }
}
