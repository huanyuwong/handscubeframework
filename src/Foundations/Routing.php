<?php

namespace Handscube\Foundations;

use Handscube\Abstracts\Features\CubeAble;
use Handscube\Kernel\Component;
use Handscube\Kernel\Exceptions\RouteException;

class Routing extends Component implements CubeAble
{

    const GET = "get";
    const POST = "post";
    const PUT = "put";
    const DELETE = "delete";
    const HEAD = "head";
    const OPTIONS = "options";

    protected static $routingTable = [];

    public function __construct()
    {

    }

    public function handle($request)
    {
        $request->parseUrl();
        $this->__registerRoute();
    }

    public function getRoutingTable()
    {
        return $this->routingTable;
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

        $type = \is_array($type) ? implode(",", $type) : $type;
        if (!$resource) {
            throw new RouteException("Routing $path requires specific paths.");
        }

        $path = \strtolower($path);
        if (array_key_exists($path, self::$routingTable)) {
            return 1;
        }
        self::$routingTable[$path] = ["type" => $type, "resource" => $resource];
        return 1;

    }

    public function name(string $name)
    {

    }

    public function setRoutingTable()
    {

    }
}
