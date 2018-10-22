<?php

namespace Handscube\Kernel;

use Handscube\Foundations\BaseComponent;
use Handscube\Handscube;

class Component extends BaseComponent
{

    protected $getter;
    protected $setter;

    // protected static $getterCollection = [];

    // public function beforeGetter()
    // {
    //     return $this->getApp();
    // }

    public function getterExists($property)
    {

        return $this->$getter ? true : false;
    }

    public function setterExists($property)
    {
        return "set" . ucfirst(strtolower($property)) ? true : false;
    }

    public function __destruct()
    {
        echo "Component __Destruct\n";
    }
}
