<?php

namespace Handscube\Console;

use App\Commands\CommandProvider;
use Handscube\Handscube;
use Handscube\Kernel\Console\CommandProvider as KernelCommandProvider;
use Symfony\Component\Console\Application;

class Kernel extends Application
{

    public static $app;
    public $dir;

    public function __construct($dir = '', $webApp = '', $name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);
        self::$app = $webApp;
        $this->dir = $dir;
        $this->registerCommand($this->dir);
    }

    /**
     * Register kernel command and user custom command.
     *
     * @param string $dir
     * @return void
     */
    public function registerCommand($dir = '')
    {
        $commands = \is_array(CommandProvider::boot())
        ? array_merge(KernelCommandProvider::boot(), CommandProvider::boot())
        : KernelCommandProvider::boot();
        foreach ($commands as $command) {
            $this->add(new $command($dir));
        }
    }
}
