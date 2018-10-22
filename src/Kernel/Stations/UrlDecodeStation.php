<?php

namespace Handscube\Kernel\Stations;

use Handscube\Kernel\Request;
use Handscube\Kernel\Station;

class UrlDecodeStation extends Station
{

    public function filter(Request $request)
    {
        // ff($request->query);
        // ff($request->uri);
        $request->uri = urldecode($request->uri);
        $request->url = urldecode($request->url);
    }
}
