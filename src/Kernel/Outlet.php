<?php

namespace Handscube\Kernel;


abstract class Outlet {

    /**
     * Array $pins.
     * Used to record the number of Pins.
     */
    protected $pins = [];

    /**
     * plug Pins to the cube.
     */
    abstract function plug($pins);
}