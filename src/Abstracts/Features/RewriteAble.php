<?php

namespace Handscube\Abstracts\Features;


interface RewriteAble {


    /**
     * Rewrite Call.
     */
    public function __call($fn,$fnParams);
}