<?php

namespace Handscube\Abstracts;

abstract class App {

    protected $cube;

    abstract function instanceCube();

    abstract static function make($name);

    abstract function handle($request);
}