<?php

namespace Handscube\Kernel\Http;

use Handscube\Assistants\Cookie;
use Symfony\Component\HttpFoundation\ResponseHeaderBag as KernelResponseBag;

class ResponseHeaderBag extends KernelResponseBag
{
    public function __construct(array $headers = array())
    {
        parent::__construct($headers);
        $this->cookies = Cookie::all();
    }
}
