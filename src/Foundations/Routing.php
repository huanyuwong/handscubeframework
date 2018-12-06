<?php

namespace Handscube\Foundations;

use Handscube\Abstracts\Features\CubeAble;
use Handscube\Assistants\Arr;
use Handscube\Handscube;
use Handscube\Kernel\Component;
use Handscube\Kernel\Exceptions\InvalidException;
use Handscube\Kernel\Response;

class Routing extends Component implements CubeAble
{

    const GET = "get";
    const POST = "post";
    const PUT = "put";
    const DELETE = "delete";
    const HEAD = "head";
    const OPTIONS = "options";

    protected static $routingTable = [];
    protected static $routingNameTable = [];
    protected static $latelyRoute = [];
    protected static $currentRegisterRoute;
    protected static $currentRegisterPath;
    protected static $currentRegisterType;
    protected static $currentRoute;
    protected static $lastRoute;

    protected static $modelsHandler = [];
    public static $modelsInstance = [];
    public static $modelsAreSort = false;
    public static $prefix = [];

    /**
     * Constructor.
     */
    public function __construct()
    {

    }

    /**
     * Handle function.Do something initaliztion.
     *
     * @param [type] $request
     * @return void
     */
    public function handle($request)
    {
        $request->parseUrl();
        $this->__registerRoute(); //register route
    }

    /**
     * Get route table.
     *
     * @return void
     */
    public static function getRoutingTable()
    {
        return self::$routingTable;
    }

    /**
     * Get route name table.
     *
     * @return void
     */
    public static function getRoutingNameTable()
    {
        return self::$routingNameTable;
    }

    /**
     * Add a route.
     *
     * @param [type] $path
     * @param [type] $resource
     * @param [mixed] $type
     * @return void
     */
    public static function addRoute($path, $resource, $type)
    {
        $path = self::havePrefix($path);
        if ($path && $resource && $type) {
            self::$currentRegisterPath = $path;
            self::$currentRegisterType = $type;
            self::$currentRegisterRoute = self::app()->request->protocol . "://" . self::app()->request->host . $path;
            if (isset(self::$routingTable[$path])) {
                if ($type === 'any') {
                    Arr::clearArr(self::$routingTable[$path]);
                    self::$routingTable[$path][$type] = ['resource' => $resource];
                    return self::this();
                }
                if (array_key_exists($type, self::$routingTable[$path])
                    || array_key_exists('any', self::$routingTable[$path])) {
                    return self::this();
                }
            }
            self::$routingTable[$path][$type] = ['resource' => $resource];
            return self::this();
        } else {
            throw new InvalidException("Wrong route syntax path $path");
        }
    }

    /**
     * Check route path whether have prefix.
     *
     * @param [type] $path
     * @return void
     */
    public static function havePrefix($path)
    {
        return count(self::$prefix) > 0 ? self::$prefix[0] . $path : $path;
    }

    /**
     * Current route.
     *
     * @return string url
     */
    public static function getCurrentRoute()
    {
        return self::$currentRoute ?: self::app()->request->url;
    }

    /**
     * Get last Route.
     *
     * @return string url
     */
    public static function getLastCurrent()
    {
        return self::$lastRoute;
    }

    /**
     * Get current register route.
     *
     * @return void
     */
    public function getCurrentRegisterRoute()
    {
        return self::$currentRegisterRoute;
    }

    /**
     * Route name
     *
     * @param string $name
     * @return void
     */
    public function name(string $name)
    {
        self::$routingNameTable[$name] = self::$currentRegisterRoute;
    }

    /**
     * Return a route url.
     *
     * @param string $routeName
     * @param array $params
     * @return void
     */
    public static function route(string $routeName, array $params = [])
    {
        if (!isset(self::$routingNameTable[$routeName])) {
            throw new InvalidException("Route name $routeName does not exists.");
        }
        if (\count($params > 0)) {
            return self::fillBrackets(self::$routingNameTable[$routeName], $params);
        } else {
            return self::$routingNameTable[$routeName];
        }
    }

    /**
     * Fill {}
     *
     * @param string $string
     * @param array $replace
     * @return void
     */
    public static function fillBrackets(string $string, array $replace)
    {
        $patterns = ['/\{.*?\}/', '/\{.*?\}/'];
        return \preg_replace($patterns, $replace, $string, 1);
    }

    /**
     * Redirect other route or a specified url.
     *
     * @param string $target
     * @param array $params
     * @return void
     */
    public static function redirect(string $target, array $params = [])
    {
        if (isset(self::$routingNameTable[$target])) {
            $url = self::route($target, $params);
            (new Response())->redirect($url);
        } else {
            (new Response())->redirect($target);
        }
    }

    /**
     * Route::prefix('admin',function(){
     *  Route::get('test','index@test');
     * })
     *
     * @param string $prefix
     * @param Closure $closure
     * @return void
     */
    public static function prefix(string $prefix, \Closure $closure)
    {
        self::$prefix[] = $prefix;
        $closure();
        array_pop(self::$prefix);
    }

    /**
     * Regex match.
     *
     * @param array $patterns
     * @return void
     */
    public static function regex(array $patterns)
    {
        self::$routingTable[self::$currentRegisterPath][self::$currentRegisterType]['pattern'] = $patterns;
        return self::this();
    }

    /**
     * Route method.
     *
     * @param string $path
     * @param string $resource
     * @return void
     */
    public static function get(string $path, $resource = '')
    {
        return self::addRoute($path, $resource, "get");
    }

    public static function post(string $path, $resource = '')
    {
        return self::addRoute($path, $resource, "post");
    }

    public static function put(string $path, $resource = '')
    {
        return self::addRoute($path, $resource, "put");
    }

    public static function patch(string $path, $resource = '')
    {
        return self::addRoute($path, $resource, "patch");
    }

    public static function delete(string $path, $resource = '')
    {
        return self::addRoute($path, $resource, "delete");
    }

    public static function any(string $path, $resource)
    {
        return self::addRoute($path, $resource, "any");
    }

    /**
     * Register route use an array.
     * e.g. Route::match(['get','post'],"/test",function(){});
     * @param array $routeType
     * @param string $path
     * @param [type] $resource
     * @return void
     */
    public static function match(array $routeType, string $path, $resource)
    {
        $mixType = '';
        foreach ($routeType as $typeItem) {
            $mixType = $mixType . $typeItem . '|';
        }
        $mixType = substr($mixType, 0, strrpos($mixType, '|'));
        self::addRoute($path, $resource, $mixType);
        return self::this();
    }

    /**
     * Register a resource(RESETful) route.
     *
     * @param string $pathName
     * @param string $controller
     * @return void
     */
    public static function resource(string $pathName, string $controller)
    {
        self::get('/' . $pathName, $controller . '@index')->name($pathName . '.index');
        self::get('/' . $pathName . '/{id}', $controller . '@show')->regex(['id' => '/\d+/'])->name($pathName . '.show');
        self::get('/' . $pathName . '/create', $controller . '@create')->name($pathName . '.create');
        self::post('/' . $pathName, $controller . '@store')->name($pathName . '.store');
        self::get('/' . $pathName . '/{id}/edit', $controller . '@edit')->regex(['id' => '/\d+/'])->name($pathName . '.edit');
        self::match(['put', 'patch'], '/' . $pathName . '/{id}', $controller . '@update')->name($pathName . '.update');
        self::delete('/' . $pathName . '/{id}', $controller . '@destroy')->regex(['id' => '/\d+/'])->name($pathName . '.destroy');
    }

    /**
     * Return singleton application.
     *
     * @return void
     */
    public static function app()
    {
        return Handscube::$app;
    }

    /**
     * Access this staticly.
     *
     * @return void
     */
    public static function this()
    {
        return self::app()->router;
    }

    /**
     * get route by route name
     *
     * @param string $routeName
     * @return void
     */
    public static function getRouteByName(string $routeName)
    {
        return self::$routingNameTable[$routeName] ?: null;
    }

    public function setRoutingTable()
    {

    }
}
