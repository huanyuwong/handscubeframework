<?php

namespace Handscube\Foundations;

use Handscube\Abstracts\Features\GSAble;
use Handscube\Kernel\Collection;
use Handscube\Traits\GSImpTrait;

abstract class BaseEvent extends Collection implements GSAble
{
    use GSImpTrait;
    public $trigger;
    public $name;

}
