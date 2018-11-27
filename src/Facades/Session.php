<?php

namespace Handscube\Facades;

use Handscube\Kernel\Session as KernelSession;

class Session extends Facade
{
    public static function apply()
    {
        return KernelSession::class;
    }
}
