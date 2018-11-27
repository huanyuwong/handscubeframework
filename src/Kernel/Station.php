<?php
namespace Handscube\Kernel;

use Handscube\Foundations\BaseStation;
use Handscube\Kernel\Request;

class Station extends BaseStation
{

    // public function runHandle(Request $request)
    // {
    //     return $this->handle($request);
    // }

    public function handle(Request $request)
    {
        return $this->filter($request);
    }

    // public function filter(Request $request)
    // {

    // }

}
