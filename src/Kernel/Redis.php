<?php

namespace Handscube\Kernel;

class Redis extends \Predis\Client
{

    use \Handscube\Traits\AppAccesserTrait;

    public function __construct($parameters = null, $options = null)
    {
        $parameters = isset($parameters) ? $parameters : config('db')['redis']['parameters'];
        $options = isset($options)
        ? $options
        : (config('db')['redis']['options']
            ?: null);
        parent::__construct($parameters, $options);
    }
}
