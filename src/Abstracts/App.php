<?php

namespace Handscube\Abstracts;

use Handscube\Kernel\Request;

abstract class App
{

    // abstract public function make($class, $isLoadDepends);

    abstract public function handle(Request $request);
}
