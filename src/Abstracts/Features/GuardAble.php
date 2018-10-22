<?php

namespace Handscube\Abstracts\Features;

use Handscube\Kernel\Request;

interface GuardAble extends RewriteAble
{

    // public function guardOf();
    public function handle(Request $request, $fnParams);

}
