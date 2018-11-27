<?php

namespace Handscube\Kernel;

use Handscube\Foundations\BaseController;
use Handscube\Traits\Validator;

class Controller extends BaseController
{
    use Validator;

    public function __construct()
    {
        $this->defaultRules();
    }

    public function __call($name, $args)
    {

    }
}
