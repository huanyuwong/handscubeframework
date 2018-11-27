<?php

namespace Handscube\Kernel;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response as KernelResponse;

class Response extends KernelResponse
{

    public function __construct($content = '', $status = 200, $headers = array())
    {
        parent::__construct($content, $status, $headers);
    }

    public function redirect($url)
    {
        (new RedirectResponse($url))->send();
    }
}
