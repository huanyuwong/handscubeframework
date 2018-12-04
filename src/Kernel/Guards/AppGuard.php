<?php

namespace Handscube\Kernel\Guards;

use Handscube\Kernel\Guard;

class AppGuard extends Guard
{

    const cate = "app";

    protected $register = [
        \Handscube\Kernel\Stations\UrlDecodeStation::class,
        // AccessTokenCheckStation::class,
    ];
}
