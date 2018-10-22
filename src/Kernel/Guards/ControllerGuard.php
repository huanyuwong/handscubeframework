<?php

namespace Handscube\Kernel\Guards;

use Handscube\Kernel\Guard;

class ControllerGuard extends Guard
{

    const cate = "controller";

    protected $register = [
        // \Handscube\Kernel\Stations\UrlDecodeStation::class,
    ];
}
