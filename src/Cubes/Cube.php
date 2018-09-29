<?php

namespace Handscube\Cubes;

use Handscube\Kernel\Outlet;
use Handscube\Abstracts\Interfaces\RequestAble;
use Handscube\Traits\ResolveTrait;

/**
 * 
 * This class is part of Handscube framework.
 * Cube Class #Handscube framework.
 * 
 * @Author Huanyu.Wong
 */

class Cube extends Outlet implements RequestAble{

    use ResolveTrait;
    
    protected $pins;
    static protected $lastInstance;


    public static function getInstance($class,$type = false){
        self::$lastInstance  = self::$lastInstance ? self::$lastInstance : self::resolve($class,$type);
        return self::$lastInstance;
    }

    public function handle($request) {

    }

    public function plug($pins)
    {
        
    }
    
}