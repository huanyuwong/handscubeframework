<?php

namespace Handscube\Kernel\Console;

use Handscube\Console\Commands\CreateControllerCommand;
use Handscube\Console\Commands\CreateModelCommand;
use Handscube\Console\Commands\CreateServerCommand;
use Handscube\Console\Commands\StartWorkerCommand;

/**
 * Boot custom console comman.
 */

class CommandProvider
{
    public static function boot()
    {
        return [
            CreateControllerCommand::class,
            CreateModelCommand::class,
            StartWorkerCommand::class,
            CreateServerCommand::class,
        ];
    }
}
