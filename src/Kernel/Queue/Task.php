<?php

namespace Handscube\Kernel\Queue;

class Task
{

    public $state = 'pending';
    public static $defaultQueue = 'tasks';
    public static $queueCollection = ['tasks'];

    protected $queueName = 'tasks';
    protected $taskId;

    public function __construct($taskId)
    {
        $this->taskId = isset($taskId) ? $taskId : substr(\hash('sha256', mt_rand(1, 10000) . time()), 0, 16);
    }

    public function handle()
    {

    }

    public function getQueueName()
    {
        return $this->queueName;
    }

    public function setQueueName(string $name)
    {
        $this->queueName = $name;
        if (!in_array($name, self::$queueCollection)) {
            self::$queueCollection[] = $name;
        }
        return true;
    }
}
