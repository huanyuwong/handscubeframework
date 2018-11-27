<?php

namespace Handscube\Foundations;

use Handscube\Abstracts\Features\CubeAble;
use Handscube\Handscube;
use Handscube\Kernel\Component;
use Handscube\Kernel\Exceptions\InvalidException;
use Handscube\Kernel\Exceptions\RouteException;
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
    protected static $currentRegisterRoute;

    protected static $modelsHandler = [];
    public static $modelsInstance = [];
    public static $modelsAreSort = false;
    public static $prefix = [];

    public function __construct()
    {

    }

    public function handle($request)
    {
        $request->parseUrl();
        $this->__registerRoute(); //register route
    }

    public static function getRoutingTable()
    {
        return self::$routingTable;
    }

    public static function getRoutingNameTable()
    {
        return self::$routingNameTable;
    }

    /**
     * add a route
     *
     * @param [type] $path
     * @param [type] $resource
     * @param [mixed] $type
     * @return void
     */
    public static function addRoute($path, $resource, $type)
    {
        if (\count(self::$prefix) > 0) {
            $path = self::$prefix[0] . $path;
        }
        $type = \is_array($type) ? implode(",", $type) : $type;
        if (!$resource) {
            throw new RouteException("Routing $path requires specific paths.");
        }

        $path = \strtolower($path);
        if (array_key_exists($path, self::$routingTable)) {
            return self::this();
        }
        self::$routingTable[$path] = ["type" => $type, "resource" => $resource];
        self::$currentRegisterRoute = self::app()->request->protocol . "://" . self::app()->request->host . $path;
        return self::this();

    }

    /**
     * Current route.
     *
     * @return void
     */
    public static function currentRoute()
    {
        return self::app()->request->url;
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

    public static function app()
    {
        return Handscube::$app;
    }

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
