<?php

namespace Handscube\Foundations;

use Handscube\Kernel\Component;
use Handscube\Kernel\Route;
use Handscube\Traits\DispatchTrait;

class BaseController extends Component
{
    use DispatchTrait;

    public function __construct()
    {
    }

    public static function model()
    {

    }

    public function response()
    {
        return new \Handscube\Kernel\Response();
    }

    public function redirect(string $target, array $params = [])
    {
        Route::redirect($target, $params);
    }

}
