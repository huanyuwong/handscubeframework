<?php

namespace Handscube\Kernel\Guards;

use Handscube\Kernel\Guard;
use Handscube\Kernel\Stations\AccessTokenCheckStation;

class ControllerGuard extends Guard
{

    const cate = "controller";

    protected $register = [
        // \Handscube\Kernel\Stations\UrlDecodeStation::class,
    ];

    protected $kernelRegister = [
        AccessTokenCheckStation::class,
    ];

    public function register()
    {
        return array_merge($this->kernelRegister, $this->register);
    }

}
