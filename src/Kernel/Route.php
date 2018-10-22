<?php

namespace Handscube\Kernel;

use App\Models\CateContents;
use Handscube\Assistants\Arr;
use Handscube\Assistants\Composer;
use Handscube\Foundations\Routing;
use Handscube\Handscube;
use Handscube\Kernel\Exceptions\InsideException;
use Handscube\Kernel\Exceptions\NotFoundException;
use Handscube\Kernel\Exceptions\RouteException;
use Handscube\Kernel\Guards\ControllerGuard;
use Handscube\Kernel\Request;
use Illuminate\Contracts\Auth\Guard;

/***
 * Route Class #Handscube.
 * This Class is part of Handscube framework.
 * @Author J.W.
 */

class Route extends Routing
{

    protected $module;
    protected $controller;
    protected $action;
    protected $params;
    protected $guard;

    const INDEX_MODULE_CTRL_SPACE = "App\Controllers";

    const APP_GUARD_SPACE = "App\Guards";

    public function __construct()
    {
        parent::__construct();
        // $this->__registerRoute();
    }

    /**
     * Register Routes.
     *
     * @return void
     */
    protected function __registerRoute()
    {
        Composer::import($this->app->getRoutePath() . "/web.php");
    }

    /**
     * Handle Request.
     *
     * @param [type] $request
     * @return void
     */
    public function handle($request)
    {
        parent::handle($request);
        $cate = CateContents::frist();
        ff($cate);
        $this->runRoute($request);

    }

    public function init()
    {

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
        self::addRoute($path, $resource, "get");
    }

    public static function post(string $path, $resource = '')
    {
        self::addRoute($path, $resource, "post");
    }

    public static function put(string $path, $resource = '')
    {
        self::addRoute($path, $resource, "put");
    }

    public static function patch(string $path, $resource = '')
    {
        self::addRoute($path, $resource, "patch");
    }

    public static function delete(string $path, $resource = '')
    {
        self::addRoute($path, $resource, "delete");
    }

    public static function any(string $path, $resource)
    {
        self::addRoute($path, "any");
    }
    /**
     * Handle request and function params to controller guard.
     *
     * @param Guard $guard
     * @param Request $request
     * @param array $fnParmas
     * @return void
     */
    public function handleToGuard(Request $request, array $fnParmas = [])
    {

        if ($this->guard->only() && $this->guard->except()) {
            throw new InsideException("Guard properties {only} and {except} can't have element at same time.");
        }
        if (in_array($this->action, $this->guard->except())) {
            return;
        }
        if ($this->guard->register()) {
            $this->checkActionStations($this->action, $request, $fnParmas);
        }
    }
    /**
     * Bind guard.
     *
     * @param string $guard
     * @return void
     */
    public function bindGuard(string $guard = '')
    {
        if (class_exists($guard)) {
            $this->guard = $this->guard ?: $this->app->make($guard, false);
            return true;
        }
        throw new NotFoundException("Guard $guard do not exists.");
    }

    /**
     * Check controller guard stations.
     *
     * @param [type] $action
     * @param Request $request
     * @param array $fnParmas
     * @return void
     */
    public function checkActionStations($action, Request $request, array $fnParmas = [])
    {
        $this->guard->handle($request, $fnParmas);
        // $this->app->make($this->guard->register(), false)->handle($request, $fnParmas);
        // $actionGuard = strtolower(($action) . "Guard");
        // if (method_exists($this->guard, $actionGuard)) {
        //     $this->guard->$actionGuard($request, $fnParmas);
        // }
    }
    /**
     * Return controller guard.
     *
     * @return void
     */
    public function guard()
    {
        return ControllerGuard::class;
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
        self::addRoute($path, $resource, $routeType);
    }

    /**
     * Register module,controller,action to this.
     *
     * @param [type] $module
     * @param [type] $controller
     * @param [type] $action
     * @param array $params
     * @return void
     */
    public function quickRegisterRoute($module, $controller, $action, $params = [])
    {
        $this->module = $module;
        $this->controller = $controller;
        $this->action = $action;
        $this->params = $params ? $params : null;
    }

    /**
     * Handle request and start running route and do some parse work.
     *
     * @param [type] $request
     * @return void
     */
    public function runRoute($request)
    {
        if (!$this->shouldAutoParse()) {
            $this->quickRegisterRoute($request->module, $request->controller, $request->action);
        }
        if ($match = $this->matchRoute($request)) {
            //Check method auth when the route is exists.
            $checkedRoute = $this->checkRouteMethod($match);
            //Parse route resource as an array.
            $resource = $this->parseResource($checkedRoute["resource"]);
            $this->quickRegisterRoute($resource["module"], $resource["controller"], $resource["action"], $checkedRoute["params"]);
            $currentGuard = $this->ensureCurrentGuard($this->module, $this->controller);
            $this->bindGuard($currentGuard);
            $this->handleToGuard($request, $this->params);
            // $this->guard->handle($request,$fnParmas);
            return array_key_exists("params", $checkedRoute) ?
            $this->callRoute($request, $resource, $checkedRoute["params"]) :
            $this->callRoute($request, $resource);
        } else {
            throw new RouteException("The route $request->uri dose not exists.");
        }
    }

    public function ensureControllerSpace(string $module, string $controller)
    {
        if ($module === "index") {
            return self::INDEX_MODULE_CTRL_SPACE . "\\" . ucfirst($controller);
        }
        return self::INDEX_MODULE_CTRL_SPACE . "\\" . ucfirst($module) . "\\" . ucfirst($controller);
    }

    /**
     * Ensure current guard.
     *
     * @param string $module
     * @param string $controller
     * @return void
     */
    public function ensureCurrentGuard(string $module, string $controller)
    {
        if ($module === "index") {
            return self::APP_GUARD_SPACE . "\\" . ucfirst($controller) . "Guard";
        }
        return self::APP_GUARD_SPACE . "\\" . ucfirst($module) . "\\" . ucfirst($controller) . "Guard";
    }

    /**
     * Match route and get function params.
     *
     * @param Request $request
     * @return void
     */
    public function matchRoute(Request $request)
    {
        foreach (self::$routingTable as $path => $resource) {
            if ($result = $this->matchOneRoute($request->uri, $path)) {
                if (is_array($result)) {
                    $resource["params"] = $result;
                    return $resource;
                }
                return $resource;
            }
        }
        return false;
    }

    /**
     * Matching route through given the request uri and route table path.
     *
     * @param [type] $uri
     * @param [type] $routePath
     * @return [bool | array] return an array contains params when matchs success,else return a bool.
     */
    public function matchOneRoute($uri, $routePath)
    {

        $matchResult = [];
        $uriSplit = Arr::drop(explode("/", $uri));
        $routeSplit = Arr::drop(explode("/", $routePath));
        if (count($uriSplit) === count($routeSplit)) {
            foreach ($routeSplit as $index => $item) {
                if (strpos($item, "{") !== false && strpos($item, "}") !== false || strpos($item, ":") !== false) {
                    $item = str_replace(["{", "}", ":"], "", $item);
                    $matchResult[] = strpos($uriSplit[$index], "?") === false ? $uriSplit[$index] : \substr($uriSplit[$index], 0, strpos($uriSplit[$index], "?"));
                    continue;
                } else {
                    if ($uriSplit[$index] !== $item) {
                        return false;
                    }
                    continue;
                }
            }
        } else {
            return false;
        }
        return $matchResult ? $matchResult : 1;

    }

    /**
     * Parse route.
     * Get module,controller and action.
     * @param string $path
     * @param [type] $resource
     * @return void
     */
    public static function parseResource($resource)
    {

        //when $resource is instanceof Closure.
        if (is_callable($resource)) {
            return $resource;
        }
        if (!empty($resource)) {
            if (is_string($resource)) {
                if (strpos($resource, "@") === false) {
                    throw new RouteException("Wrong Router $resource");
                }
                $isModule = '';
                if ($isModule = strpos($resource, '.')) {
                    $split = explode('.', $resource);
                    $resource = $split[1];
                }
                $module = $isModule === false ? "index" : strtolower($split[0]);
                $controller = strtolower(explode("@", $resource)[0]);
                $action = strtolower(explode("@", $resource)[1]);
                $parseRes = ["module" => $module, "controller" => $controller, "action" => $action];
                return $parseRes;
            }
        }
        return 0;
    }

    /**
     * Check current request type whether macth specified route.
     *
     * @param [type] $matches [the matchs that match route]
     * @return void
     */
    public function checkRouteMethod($matches)
    {
        if (strpos($matches["type"], ",") !== false) {
            $types = explode(",", $matches["type"]);
            foreach ($types as $type) {
                if ($this->app->request->requestType === $type) {
                    $matches["type"] = $type;
                    return $matches;
                }
            }
        } else if ($this->app->request->requestType === $matches["type"] && $matches["type"] !== "any") {
            return $matches;
        } else if ($matches["type"] === "any") {
            return $matches;
        } else {
            throw new RouteException("The route access denied cause request type is not allowed in this case.");
        }

    }

    public function callRoute($request, $routeEntity, $params = [])
    {
        if (is_callable($routeEntity)) {
            return $params ? $routeEntity(...$params) : $routeEntity();
        }
        $this->quickRegisterRoute($routeEntity["module"], $routeEntity["controller"], $routeEntity["action"], $params);
        // if ($guard = $this->haveControllerGuard($this->module, $this->controller, $this->action)) {
        //     (new $guard)->handle($request);
        // }
        ff($this->params);
    }

    /**
     * Check the controller guard whether exists.
     *
     * @param [type] $module
     * @param [type] $controller
     * @param [type] $action
     * @return void
     */
    public function haveControllerGuard($module, $controller, $action)
    {
        if ($module == "index") {
            $guard = self::APP_GUARD_SPACE . "\\" . ucfirst($controller) . "Guard";
            return class_exists($guard) ? $guard : 0;
        }
        $guard = self::APP_GUARD_SPACE . "\\" . $module . "\\" . ucfirst($controller) . "Guard";
        return class_exists($guard) ? $guard : 0;
    }

    /**
     * wipe of {}
     *
     * @param [type] $path
     * @return void
     */
    public static function getClearRoute($path)
    {
        $replacedPath = '';
        if (strpos($path, "{")) {
            // preg_match_all("/\{([a-zA-z]+)\}/i",$path,$matches);
            $patterns = ["/(\{)+/", "/(\})+/"];
            $replacedPath = \preg_replace($patterns, '', $path);
        } else { $replacedPath = $path;}
        return $replacedPath;
    }

    /**
     * Check whether should open request url auto parse to get mould,action.
     *
     * @return void
     */
    public function shouldAutoParse()
    {
        return $this->app->appConfig["router"]["auto_parse"] ? 1 : 0;
    }

    /**
     * get route resource by path.
     *
     * @param [string] $path
     * @return void [return resource when it exists,otherwise return fasle]
     */
    public function getRouteByPath($path)
    {
        if (array_key_exists($path, self::$routingTable)) {
            return self::$routingTable($path);
        }
        return false;
    }

    public function __destruct()
    {
        echo "Route __destruct!\n";
    }
}
