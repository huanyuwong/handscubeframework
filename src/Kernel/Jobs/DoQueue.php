<?php

namespace Handscube\Kernel\Jobs;

use Handscube\Kernel\Queue\Consume;
use Predis\Client;

class DoQueue extends Job
{

    protected $data;
    protected $redis;

    public function __construct($params)
    {
        $this->redis = new Client([
            'host' => '127.0.0.1',
            'port' => 6379,
            'database' => 0,
        ]);
        $this->data = $params;
    }

    public function work($params)
    {
        $params = $params ?: $this->data;
        $r = $this->redis->rpop('tasks');
        print_r($r);
        exit();
        // return function () use ($params) {
        if (is_array($params) && count($params) > 0) {
            foreach ($params as $queue) {
                $task = unserialize($this->redis->rpop($queue));
                $task->state = 'done';
                Consume::exec($task);
                sleep(1);
            }
        } elseif ($params) {
            $task = $this->redis->rpop('tasks');
            // $task->state = 'done';
            print_r($task);
            exit();
            Consume::exec($task);
            sleep(1);
        }
        // };
    }

    public function getData()
    {
        return $this->data ?: null;
    }
}
