<?php

namespace Handscube\Kernel;

use Handscube\Foundations\BaseComponent;
use Handscube\Handscube;
use Handscube\Kernel\Session;

/**
 * Class Component [c] Handscube.
 * @author J.W.
 */
class Component extends BaseComponent
{

    protected $getter;
    protected $setter;

    /**
     * Determine if the getter exist.
     * e.g. Now there have a variable $foo，
     * The component will first call the getter function like getFoo();
     *
     * @param [type] $property
     * @return void
     */
    public function getterExists($property)
    {
        return $this->$getter ? true : false;
    }

    /**
     * Determine if the setter exist.
     *
     * @param [type] $property
     * @return void
     */
    public function setterExists($property)
    {
        return "set" . ucfirst(strtolower($property)) ? true : false;
    }

    public function isGuest()
    {
        return isEmpty(Session::get("username"));
    }
}
