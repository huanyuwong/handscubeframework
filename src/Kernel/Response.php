<?php

namespace Handscube\Kernel;

use Handscube\Assistants\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response as KernelResponse;

class Response extends KernelResponse
{
    public function __construct($content = '', $status = 200, $headers = array())
    {
        parent::__construct($content, $status, $headers);
    }

    // public function rewriteHeaders($headers)
    // {
    //     $this->headers = new ResponseHeaderBag($headers);
    // }

    public function redirect($url)
    {
        (new RedirectResponse($url))->send();
    }

    public function cookie($name, $value, $expire = 0, $path = '/', $domain = '', $secure = false, $httpOnly = false)
    {
        Cookie::set($name, $value, $expire, $path, $domain, $secure, $httpOnly);
    }

    public function withCookie(Cookie $cookie)
    {
        return $cookie->save();
    }

    /**
     * Send json data.
     *
     * @param array $data
     * @return void
     */
    public function withJson(array $data = [])
    {
        $this->headers->set('Content-Type', 'application/json');
        if ($this->content) {
            return $this->setContent(json_encode($this->content));
        }
        return $this->setContent(json_encode($data));
    }
}
