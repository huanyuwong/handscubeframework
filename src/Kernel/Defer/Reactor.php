<?php

namespace Handscube\Kernel\Defer;

use Handscube\Console\Kernel;
use Handscube\Kernel\Exceptions\InsideException;
use Handscube\Kernel\Queue\Task;
use Handscube\Kernel\Redis;

/**
 * Class Reactor
 */

class Reactor
{

    public $state = 'STOP';
    public $app;
    private static $queueCollection = [];

    public function __construct()
    {
        error_reporting(E_ALL);
        set_time_limit(0);
        ob_implicit_flush();
        declare (ticks = 1);
        $this->app = Kernel::$app;
        // $this->startAtBackend();
        // $this->registerSignal([SIGTERM, SIGINT, SIGCHLD], $this->signalHandler());
        $this->state = 'START';
        $this->run();

    }

    public function startAtBackend()
    {
        $pid = pcntl_fork();
        if ($pid == 0) {
            posix_setsid();
            chdir('/');
            umask(0);
            return posix_getpid();
        } else if ($pid > 0) {
            exit();
        } else {
            throw new InsideException("Reactor::runbackend fork process faild!");
        }
    }

    public function run()
    {
        $redis = new Redis();
        while (1) {
            foreach (Task::$queueCollection as $queue) {
                if ($redis->llen($queue)) {
                    $thread = new class extends \Thread
                    {
                        public function run()
                        {
                            $redis = new Redis();
                            echo $redis->rpop('tasks') . "\n";
                        }
                    };
                    $thread->start() && $thread->join();
                }
            }
            sleep(3);
        }
    }

    public function registerSignal(array $signal, $handler)
    {
        if (is_array($signal)) {
            if ($handler) {
                foreach ($signal as $sigItem) {
                    pcntl_signal($sigItem, $handler);
                }
            }
        }
    }

    public function signalHandler($sig = null)
    {
        return function ($signal) use ($sig) {
            switch ($signal) {
                case SIGTERM:
                case SIGINT:
                    exit();
                    break;
                case SIGCHLD:
                    pcntl_waitpid(-1, $status);
                    break;
            }
        };
    }
}
