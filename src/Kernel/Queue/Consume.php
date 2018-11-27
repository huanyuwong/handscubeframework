<?php

namespace Handscube\Kernel\Queue;

use Handscube\Kernel\Queue\Task;
use Handscube\Traits\AppAccesserTrait;

class Consume
{
    use AppAccesserTrait;

    protected $currentTask;
    protected $taskContainer = [];

    public function __construct()
    {
        $this->$currentTask = null;
    }

    public function addTask(Task $task)
    {

    }

    public function delTask()
    {

    }

    public static function exec(Task $task)
    {
        self::app()->call($task, 'handle');
    }

}
