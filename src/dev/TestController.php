<?php

namespace Handscube\Dev;

use Handscube\Dev\Request;
use Handscube\Dev\Db;

class TestController {

    function __construct(Request $request,Db $db)
    {
        $request->showRequest();
        $db->showDb();
    }

    function test(){
        echo "test method from Handscube\Dev\TestController.";
    }
}