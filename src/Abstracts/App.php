<?php

namespace Handscube\Abstracts;

use Handscube\Abstracts\Features\SimulateContainer;
use Handscube\Kernel\Request;

abstract class App implements SimulateContainer
{

    // abstract public function make($class, $isLoadDepends);

    abstract public function handle(Request $request);

    public function bound($key)
    {
        if ($key !== 'Specified') {
            return false;
        }
        return true;
    }
}
