<?php

namespace Handscube\Traits;

use Handscube\Facades\Redis;
use Handscube\Kernel\Queue\Task;

trait DispatchTrait
{

    public function dispatch(Task $task)
    {
        $queue = $task->getQueueName();
        if (Redis::lpush($queue, serialize($task))) {
            return true;
        }
        return false;
    }
}
