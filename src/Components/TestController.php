<?php

namespace Handscube\Components;

use Handscube\Kernel\Guard;
use Handscube\Kernel\Request;

class TestController extends Guard
{

    public function __construct($guardSpace = '')
    {
        $this->guardSpace = $guardSpace ? $guardSpace : $this->app::__CTRL_NAMESPACE__;

    }

    public function __get($key)
    {
        return parent::__get($key);
    }

    public function test(TestReflection $test)
    {
        print_r($test->request);
    }

    public function testTwice(Request $request)
    {
        echo $request->name . "\n";
    }

}

class TestReflection
{

    public $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}
