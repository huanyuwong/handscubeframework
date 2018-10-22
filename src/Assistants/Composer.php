<?php

namespace Handscube\Assistants;

use Handscube\Abstracts\Features\FastcallAble;

class Composer extends Assistant implements FastcallAble{


    public function __construct(){

        
    }

    public static function apply(){
        return __CLASS__;
    }

    public static function use($path){
        return self::import($path,true);
    }

    public static function import(string $path, bool $shouldReturn = false){
        if($shouldReturn){
            return require_once $path;
        }
            require_once $path;       
    }
}