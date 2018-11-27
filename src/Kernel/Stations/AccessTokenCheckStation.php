<?php

namespace Handscube\Kernel\Stations;

use Handscube\Kernel\CrossGate;
use Handscube\Kernel\Station;

class AccessTokenCheckStation extends Station
{

    // public function filter(Request $request)
    // {
    //     // ff($request->query);
    //     // ff($request->uri);
    //     $request->uri = urldecode($request->uri);
    //     $request->url = urldecode($request->url);
    // }

    public function handle(\Handscube\Kernel\Request $request)
    {
        if ($request->requestType == 'get') {
            if (!isset($request->header['Origin']) && !isset($request->header['Referer'])) {
                // ff($request->header);
                return;
            }
        }
        if (CrossGate::verifyAccessToken($request->header['Access-Token'], $request->input()) === true) {
            ff("access granted!");
        } else {
            echo "Auth fail";
        }

    }
}
