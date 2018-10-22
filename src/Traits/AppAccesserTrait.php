<?php

namespace Handscube\Traits;

use Handscube\Handscube;

/**
 * To implent this trait you can access app like $this->app.
 */
trait AppAccesserTrait
{
    use GSImpTrait;

    public function getApp()
    {
        return Handscube::$app;
    }
}
