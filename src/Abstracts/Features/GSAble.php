<?php

namespace Handscube\Abstracts\Features;

/**
 * Make class have Getter and Setter feature.
 */

interface GSAble{

    function __get($property);

    function __set($propery,$value);

}