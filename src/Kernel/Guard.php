<?php

namespace Handscube\Kernel;

use Handscube\Abstracts\Features\AppAccesser;
use Handscube\Abstracts\Features\GuardAble;
use Handscube\Kernel\Request;
use Handscube\Traits\AppAccesserTrait;

/**
 * Guard class [c]Handscube framework
 * @author J.W. <email@email.com>
 */
class Guard implements GuardAble, AppAccesser
{
    use AppAccesserTrait;

    const type = "Guard";

    public function __construct()
    {

    }
    /**
     * register stations.
     *
     * @var array
     */
    protected $register = [

    ];

    /**
     * Except actions.
     *
     * @var array
     */
    protected $except = [

    ];

    /**
     * Only actions.
     */

    protected $only = [

    ];

    /**
     * "actionName" => ["stationName","stationName2"...];
     *
     * @var array
     */
    protected $specified = [

    ];

    /**
     * return registed stations.
     *
     * @return void
     */
    public function register()
    {
        return $this->register;
    }

    /**
     * Handle request.
     *
     * @param Request $request
     * @param [type] $params
     * @return void
     */
    public function handle(Request $request, $params = [], $stations = [])
    {
        if (!$stations) {
            return $this->simpleHandle($request, $params);
        }
        foreach ($stations as $station) {
            $r = $this->app->make($station, false)->handle($request, ...$params);
            if ($r === false) {
                throw new \Handscube\Kernel\Exceptions\AuthException("Controller station {$station} check failed.");
            }
        }

    }

    /**
     * Simple handle request withou give specified stations.
     *
     * @param Request $request
     * @param array $params
     * @return void
     */
    public function simpleHandle(Request $request, $params = [])
    {
        if ($this->register()) {
            foreach ($this->register() as $station) {
                $r = $this->app->make($station, false)->handle($request, ...$params);
            }
            if ($r === false) {
                throw new \Handscube\Kernel\Exceptions\AuthException("Controller station {$station} check failed.");
            }
        }
    }

    /**
     * Except action that guard will not check.
     *
     * @return void
     */
    public function except()
    {
        return $this->except;
    }

    /**
     * Only action that the guard will check only.
     *
     * @return void
     */
    public function only()
    {
        return $this->only;
    }

    /**
     * Assign specific stations to a action.
     *
     * @return void
     */
    public function specified()
    {
        return $this->specified;
    }

    public function __call($fn, $fnParams)
    {

    }

    public static function apply()
    {
        return __CLASS__;
    }

}
