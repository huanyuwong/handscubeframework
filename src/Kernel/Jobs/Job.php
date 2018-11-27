<?php

namespace Handscube\Kernel\Jobs;

abstract class Job
{

    /**
     * Undocumented function
     *
     * @return Closure
     */
    abstract public function work($params);
}
