<?php

namespace Handscube\Kernel;

use Handscube\Assistants\Arr;
use Handscube\Foundations\Routing;
use Handscube\Handscube;
use Handscube\Kernel\Exceptions\AuthException;
use Handscube\Kernel\Exceptions\InsideException;
use Handscube\Kernel\Exceptions\InvalidException;
use Handscube\Kernel\Exceptions\NotFoundException;
use Handscube\Kernel\Exceptions\RouteException;
use Handscube\Kernel\Guard;
use Handscube\Kernel\Guards\ControllerGuard;
use Handscube\Kernel\Request;

/***
 * Route Class #Handscube.
 * This Class is part of Handscube framework.
 * @Author J.W.
 */

class Route extends Routing
{

    const MAX_RECORD_ROUTE = 10;

    protected $module;
    protected $controller;
    protected $action;
    protected $params = [];
    public $paramsName = [];
    public $paramsWithoutKey = [];
    public $paramsWithKey = [];

    protected $boundModels = [];
    protected $boundParamsWithKey = [];
    protected $boundParamsWithoutKey = [];
    protected $guard;
    protected $actionGuard;

    public $currentModelBund = [];

    const INDEX_MODULE_CTRL_SPACE = "App\Controllers";

    const APP_GUARD_SPACE = "App\Guards";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Register Routes.
     *
     * @return void
     */
    protected function __registerRoute()
    {
        // Composer::import($this->app->getRoutePath() . "/web.php");
        require_once $this->app->getRoutePath() . '/web.php';
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
        return $this->runRoute($request);
    }

    /**
     * Handle request and function params to controller guard.
     *
     * @param Guard $guard
     * @param Request $request
     * @param array $fnParmas
     * @return void
     */
    public function handleToGuard(Request $request, array $fnParams = [])
    {
        if ($stations = $this->ensureCurrentActionStations()) {
            $this->dispathcCurrentActionStations($request, $fnParams, $stations);
        }
        $this->callControllerGuardMethod($fnParams);
        return;
    }

    /**
     * Call action guard.
     *
     * @param [type] $params
     * @return void
     */
    public function callControllerGuardMethod($params)
    {
        if (self::$modelsInstance) {
            self::sortModelsInstance();
        }
        if (method_exists($this->guard, $this->actionGuard)) {
            $this->app->call($this->guard, $this->actionGuard, $params);
        }
    }

    /**
     * Ensure current action stations.
     *
     * @return void
     */
    public function ensureCurrentActionStations()
    {
        $specifiedStations = $this->ensureSpecifiedStations();
        if (in_array($this->action, $this->guard->only()) || !in_array($this->action, $this->guard->except())) {
            if ($this->guard->specified() && array_key_exists($this->action, $this->guard->specified())) {
                return array_merge($this->guard->register(), $specifiedStations);
            } else {
                return $this->guard->register();
            }
        }
        if (in_array($this->action, $this->guard->except()) && $this->guard->specified() && array_key_exists($this->action, $this->guard->specified())) {
            return $specifiedStations;
        }
        return;
    }

    /**
     * Ensure spacifid stations in assoc guard.
     *
     * @return void
     */
    public function ensureSpecifiedStations()
    {
        array_key_exists($this->action, $this->guard->specified()) == false
        ? null
        : (is_string($this->guard->specified()[$this->action])
            ? [$this->guard->specified()[$this->action]]
            : $this->guard->specified()[$this->action]);
    }

    /**
     * Dispatch stations to controller guard.
     *
     * @param Request $request
     * @param array $params
     * @param array $stations
     * @return void
     */
    public function dispathcCurrentActionStations(Request $request, array $params, array $stations)
    {
        $this->checkBoundModel($this->guard);
        $this->guard->handle($request, $params, $stations);
    }

    /**
     * Bind guard instance.
     *
     * @param string $guard
     * @return void
     */
    public function bindGuard(string $guard = '')
    {
        if (class_exists($guard)) {
            $this->guard = $this->guard ?: $this->app->make($guard, false);
            return ($this->guard instanceof Guard);
        }
        return false;
    }

    public function bindCurrentActionGuard($action)
    {
        $this->actionGuard = $action . "Guard";
    }

    /**
     * Check and inject a assoc model.
     *
     * @param [type] $className
     * @return void
     */
    public function checkModelInject($className)
    {
        if ($className::type && $className::type === "Model") {
            $this->injectsArr = $this->injectsArr ?: array_chunk($this->router->paramsWithKey, 1, true);
            if ($this->injectsArr) {
                $injectArr = array_shift($this->injectsArr);
                ModelInjector::inject($className, $injectArr);
            }
        }
    }

    /**
     * Bind model
     *
     * @param [string|array] $modelName
     * @param [Closure] $fn
     * @return [function return]
     */
    // public function bindModel($modelName, \Closure $fn)
    // {
    //     if (!$this->paramsWithKey) {
    //         return false;
    //     }
    //     if (is_array($modelName) && !empty($modelName)) {
    //         return $this->bindModelWithArr($modelName, $fn);
    //     }
    //     return $this->bindModelWithString($modelName, $fn);
    // }

    /**
     * Bind model in array.
     *
     * @param array $modelName
     * @param \Closure $fn
     * @return [function return]
     */
    public function bindModelWithArr(array $modelName, \Closure $fn)
    {
        $modelInject = [];
        foreach ($modelName as $name) {
            $this->boundModels[] = $name;
            if (array_key_exists($name, $this->paramsWithKey) && $this->paramsWithKey[$name]) {
                $modelInject[] = $this->paramsWithKey[$name];
            } else {
                throw new InsideException("request param $name is not exists.");
            }
        }
        return $fn(...$modelInject);
    }

    /**
     * Bind model in string type.
     *
     * @param string $modelName
     * @param \Closure $fn
     * @return [function return]
     */
    public function bindModelWithString(string $modelName, \Closure $fn)
    {
        $modelName = strtolower($modelName);
        if (is_string($modelName)) {
            if (array_key_exists($modelName, $this->paramsWithKey) && $this->paramsWithKey[$modelName] !== false) {
                $this->boundModels[] = $modelName;
                return $fn($this->paramsWithKey[$modelName]);
            }
            throw new InsideException("request param $modelName is not exists.");
        }
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
        $this->checkBoundModel($this->guard);
        $this->guard->handle($request, $fnParmas);
    }

    /**
     * Check specified class whether have model method.
     *
     * @param [type] $class
     * @return void
     */
    public function checkBoundModel($class)
    {
        if (method_exists($class, "model")) {
            call_user_func([$class, "model"]);
            return true;
        }
        return false;
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
        $this->paramsWithKey = $params ? $params[1] : null;
        $this->paramsName = $params ? array_keys($params[1]) : null;
        $this->paramsWithoutKey = $params ? $params[0] : null;
        $this->params = $params;
    }

    /**
     * Handle request and start running route and do some parse work.
     *
     * @param [type] $request
     * @return void
     */
    public function runRoute($request)
    {
        // if (!$this->shouldAutoParse()) {
        //     $this->quickRegisterRoute($request->module, $request->controller, $request->action);
        // }
        if ($match = $this->matchRoute($request)) {
            $resource = $this->parseMatchedRoute($match);
            // ff($resource);
            $request->pathInfo = isset($this->params[1]) ? $this->params[1] : [];
            if ($this->registerControllerGuard($this->module, $this->controller)) {
                $this->bindCurrentActionGuard($this->action);
                $this->paramsWithoutKey
                ? $this->handleToGuard($request, $this->paramsWithoutKey)
                : $this->handleToGuard($request);
            }
            return $this->paramsWithKey
            ? $this->callRoute($request, $resource, $this->paramsWithKey)
            : $this->callRoute($request, $resource);
        } else {
            throw new RouteException("The route $request->uri dose not exists.");
        }
    }

    /**
     * Parse matched route.
     *
     * @param [type] $matchRoute
     * @return void
     */
    public function parseMatchedRoute($matchRoute)
    {
        $checkedRoute = $this->checkRouteMethod($matchRoute);
        $currentRequestType = $this->app->request->requestType;
        $resource = $this->parseResource($checkedRoute[$currentRequestType]["resource"]);
        // ff($resource); ['module'=>'index','controller','action'];
        $resource['params'] = isset($checkedRoute[$currentRequestType]["params"]) ? $checkedRoute[$currentRequestType]["params"] : [];
        $this->quickRegisterRoute($resource["module"], $resource["controller"], $resource["action"], $resource['params']);
        return $resource;
    }

    /**
     * Get controller space in assoc controller.
     *
     * @param string $module
     * @param string $controller
     * @return void
     */
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
    public function registerControllerGuard(string $module, string $controller)
    {
        if ($module === "index") {
            $guard = self::APP_GUARD_SPACE . "\\" . ucfirst($controller) . "Guard";
        } else {
            $guard = self::APP_GUARD_SPACE . "\\" . ucfirst($module) . "\\" . ucfirst($controller) . "Guard";
        }
        return $this->bindGuard($guard);
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
            $trueUri = (strpos($request->uri, '?') !== false)
            ? substr($request->uri, 0, strpos($request->uri, '?'))
            : $request->uri;
            if ($result = $this->matchOneRoute($trueUri, $path)) {
                if (is_array($result)) {
                    foreach ($resource as $type => $item) {
                        if ($type === 'get' || strpos($type, 'get') !== false) {
                            $resource[$type]['params'] = $result;
                        }
                    }
                    return $resource;
                }
                return $resource;
            }
        }
        return false;
    }

    // resource
    //  Array
    // (
    //    [type] => get
    //   [resource] => user@show
    //  )

//result
    // Array
    // (
    //     [0] => Array
    //         (
    //             [0] => 2
    //         )

//     [1] => Array
    //         (
    //             [id] => 2
    //         )

// )

    /**
     * Matching route through given the request uri and route table path.
     *
     * @param [type] $uri
     * @param [type] $routePath
     * @return [bool | array] return an array contains params when matchs success,else return a bool.
     */
    public function matchOneRoute($uri, $routePath)
    {
        $matchWithoutKey = [];
        $matchWithKey = [];
        $uriSplit = Arr::drop(explode("/", $uri));
        $routeSplit = Arr::drop(explode("/", $routePath));
        if (count($uriSplit) === count($routeSplit)) {
            foreach ($routeSplit as $index => $item) {
                if (strpos($item, "{") !== false && strpos($item, "}") !== false || strpos($item, ":") !== false) {
                    $item = str_replace(["{", "}", ":"], "", $item);
                    $matchWithoutKey[] = strpos($uriSplit[$index], "?") === false ? $uriSplit[$index] : \substr($uriSplit[$index], 0, strpos($uriSplit[$index], "?"));
                    $matchWithKey[$item] = strpos($uriSplit[$index], "?") === false ? $uriSplit[$index] : \substr($uriSplit[$index], 0, strpos($uriSplit[$index], "?"));
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
        return $matchWithoutKey ? [$matchWithoutKey, $matchWithKey] : 1;

    }

    /**
     * Parse route.
     * Get module,controller and action.
     * @param string $path
     * @param [type] $resource
     * @return void
     */
    public function parseResource($resource)
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
        foreach ($matches as $typeIdx => $match) {
            if ($typeIdx === 'any'
                || $typeIdx === $this->app->request->requestType) {
                return [$this->app->request->requestType => $match];
            } elseif (strpos($typeIdx, '|') !== false) {
                $types = explode("|", $typeIdx);
                foreach ($types as $type) {
                    if ($this->app->request->requestType === $type) {
                        return [$type => $match];
                    }
                }
            } else {
                throw new AuthException("Access denied cause request type is not allowed to access the route.");
                return false;
            }
        }

    }

    /**
     * After guard checkd, handle this request to controller.
     *
     * @param [type] $request
     * @param [type] $routeEntity
     * @param array $params
     * @return void
     */
    public function callRoute(Request $request, array $routes, array $params = [])
    {
        $ctrlName = $this->moduleCtrlSpace($routes["module"]) . "\\" . ucfirst($routes["controller"]) . "Controller";
        if (class_exists($ctrlName)) {
            if (method_exists($ctrlName, "model")) {
                call_user_func([$ctrlName, "model"]); //Call controller model method.
            }
            if (self::$modelsInstance) {
                $this->sortModelsInstance();
            }
            return $this->paramsWithoutKey
            ? $this->callAction($ctrlName, $routes["action"], $this->paramsWithoutKey)
            : $this->callAction($ctrlName, $routes['action']);
        } else {
            throw new NotFoundException("Controller $ctrlName dose not exists.");
        }
    }

    /**
     * Sort modelsInstance.
     *
     * @return void
     */
    public static function sortModelsInstance()
    {
        if (self::$modelsAreSort !== true) {
            self::$modelsInstance = Arr::sortWithKeyArray(self::this()->paramsName, self::$modelsInstance);
            self::$modelsAreSort = true;
        }
    }

    public static function shiftModel()
    {
        return array_shift($self::$modelsInstance);
    }

    /**
     * Call action.
     *
     * @param string $controller
     * @param string $action
     * @param array $params
     * @return void
     */
    public function callAction(string $controller, string $action, array $params = [])
    {
        if ($this->checkControllerAndAction($controller, $action) === true) {
            $this->app->isControllerCall = true;
            return $this->app->call($controller, $action, $params);
        }
    }

    /**
     * Check a controller or an action whether exists.
     *
     * @param string $controller
     * @param string $action
     * @return void
     */
    public function checkControllerAndAction(string $controller, string $action)
    {
        if (!class_exists($controller)) {
            throw new NotFoundException('Controller ' . $controller . "is not exists!");
        }
        if (method_exists($controller, $action)) {
            return true;
        }
        throw new NotFoundException('Action ' . $action . ' is not exists in ' . 'Controller ' . $controller);
    }

    /**
     * Bind request param to model instance array.
     *
     * @param string $requestParam
     * @param \Closure $fn
     * @return void
     */
    public static function bind(string $requestParam, \Closure $fn)
    {
        if (!is_array(self::this()->paramsName)) {
            return;
        }
        // ff($requestParam);
        if (!\in_array($requestParam, self::this()->paramsName)) {
            return;
        };
        if (!$fn($requestParam)) {
            throw new InvalidException("Bind a invlid model!");
        };
        self::$modelsInstance[$requestParam] = $fn($requestParam);
    }

    /**
     * Return model instances.
     *
     * @return void
     */
    public function getModelsInstance()
    {
        return self::$modelsInstance;
    }

    /**
     * Instance route models by self::$modelsHandler.
     *
     * @return void
     */
    public function instanceRouteModels()
    {
        $modelsInstance = [];
        if (self::$modelsInstance) {
            return;
        }
        foreach (self::$modelsHandler as $requestParam => $fn) {
            if (!self::$modelsInstance[$requestParam]) {
                $modelsInstance[$requestParam] = $this->injectModels($requestParam, $fn);
            }
        }
        self::$modelsInstance = Arr::sortWithKeyArray($this->paramsName, $modelsInstance);
    }

    /**
     * return What Closure return.
     *
     * @param string $key
     * @param \Closure $fn
     * @return void
     */
    public function injectModels(string $key, \Closure $fn)
    {
        return $fn($key);
    }

    /**
     * Check controller whether had bind model.
     *
     * @param [type] $controller
     * @return boolean
     */
    public function isControllerBindModel($controller)
    {
        return method_exists($controller, "model");
    }

    /**
     * Get module controller namespace
     *
     * @param string $module
     * @return void
     */
    public function moduleCtrlSpace(string $module = "index")
    {
        if ($module === "index") {
            return $this->indexModuleCtrlSpace();
        }
        return $this->commonModuleCtrlSpace($module);
    }

    /**
     * index module controller namespace.
     *
     * @return void
     */
    public function indexModuleCtrlSpace()
    {
        return self::INDEX_MODULE_CTRL_SPACE;
    }

    /**
     * other moudle controller namespace.
     *
     * @param [type] $module
     * @return void
     */
    public function commonModuleCtrlSpace($module)
    {
        return self::INDEX_MODULE_CTRL_SPACE . "\\" . ucfirst(strtolower($module));
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

    public function showResponse($actionFn)
    {
        // echo $actionF
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
}
