<?php

namespace Handscube\Assistants;

use Handscube\Abstracts\Features\FastcallAble;



class Cookie extends Assistant implements FastcallAble{


    public function __construct(){

        
    }

    public static function apply(){
        return __CLASS__;
    }

    public function find(){
        echo "Calling find method from COOKIE OBJECT!";
    }


}