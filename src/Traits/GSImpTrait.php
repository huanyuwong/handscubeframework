<?php

namespace Handscube\Traits;

trait GSImpTrait
{
    public function __get($key)
    {
        if (method_exists($this, "beforeGetter")) {
            if ($this->beforeGetter($key)) {
                return $this->beforeGetter($key);
            }
            $this->beforeGetter($key);
        }
        $getter = "get" . ucfirst($key);
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }
    }

    public function __set($key, $value)
    {

    }
}
