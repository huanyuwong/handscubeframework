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
        echo "Guard __construct!\n";
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
     * return registed stations.
     *
     * @return void
     */
    public function register()
    {
        // return $this->add ? array_merge($this->register, $this->add) : $this->register;
        return $this->register;
    }

    public function handle(Request $request, $params)
    {

        if ($stations = $this->register()) {
            foreach ($stations as $station) {
                $r = $this->app->make($station, false)->handle($request, ...$params);
                if ($r === false) {
                    throw new \Handscube\Kernel\Exceptions\AuthException("Controller station {$station} check failed.");
                }
            }
        }

    }

    public function except()
    {
        return $this->except;
    }

    public function only()
    {
        return $this->only;
    }

    public function __call($fn, $fnParams)
    {

    }

    public static function apply()
    {
        return __CLASS__;
    }

    public function guardOf()
    {

    }
}
