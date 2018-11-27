<?php

namespace Handscube\Kernel\Http;

use Handscube\Kernel\Collection;

/**
 * Hader Class [c]Handscube.
 *
 * @author 'J.W'
 */

class Header extends Collection implements \Handscube\Abstracts\Features\HeaderAble
{
    public function add($key, $value)
    {
        return $this->setnx($key, $value);
    }

    public function __get($key)
    {
        return $this->container[ucfirst($key)] ?: null;
    }

    public function __set($key, $value)
    {
        return $this->add($key, $value);
    }
}
