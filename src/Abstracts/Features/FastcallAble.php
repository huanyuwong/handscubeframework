<?php

namespace Handscube\Abstracts\Features;


/**
 * Feature fastcall able.
 * 
 * Use this interface to implement call method staticlly that are not static.
 */

interface FastcallAble{

    /**
     * Return a class name that you want to instance it so that you call the method staticly.
     * 
     * e.g return __CLASS__;
     */
    static function apply();

}