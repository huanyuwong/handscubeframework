<?php

namespace Handscube\Abstracts\Features;

use Handscube\Kernel\Request;

/**
 * Interface GuardAble
 */
interface GuardAble extends RewriteAble
{

    public function handle(Request $request, $fnParams, $stations);

}
