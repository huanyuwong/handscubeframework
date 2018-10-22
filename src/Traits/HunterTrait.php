<?php


namespace Handscube\Traits;

/**
 * Hunter trait is a Common Library used by Application.
 */

Trait HunterTrait {

    static function componentExists($component){
        if(class_exists($component)){
            return true;
        }
        return false;
    }
}