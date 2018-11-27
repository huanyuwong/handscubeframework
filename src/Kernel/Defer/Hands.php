<?php

namespace Handscube\Kernel\Defer;

class Hands extends \Thread
{

    protected $job;
    protected $redis;

    public function __construct($redis)
    {
        // $this->job = $job;
        $this->redis = $redis;
    }

    public function run()
    {
        // $this->job->work($this->job->getData());
        echo $this->redis->rpop('tasks') . "\n";
    }
}
