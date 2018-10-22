<?php

namespace Handscube\Dev;

use Handscube\Kernel\Component;

class DevComponent extends Component{

    protected $devProperty = "devTest";

    public function helloDev(){

    }

    public function __destruct(){
        echo "Dev ___destruct!\n";
    }
}