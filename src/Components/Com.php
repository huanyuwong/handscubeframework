<?php

namespace Handscube\Components;

use Handscube\Kernel\Component;


class Com extends Component {


    function index(){
        // print_r(Handscube\Handscube::$app);
        print_r($this->app);
    }
}

