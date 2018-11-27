<?php

namespace Handscube\Facades;

use Handscube\Kernel\Redis as KernelRedis;

class Redis extends Facade
{

    public static function apply()
    {
        return KernelRedis::class;
    }
}
