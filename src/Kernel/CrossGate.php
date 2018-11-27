<?php

namespace Handscube\Kernel;

use Handscube\Assistants\Encrypt;
use Handscube\Traits\AppAccesserTrait;

class CrossGate
{
    use AppAccesserTrait;

    protected static $allowDomains = [

    ];

    private static $accessToken;

    public static function allow(string $url)
    {

    }

    public static function openCross(array $domainConfig)
    {
        if (!isset($_SERVER["HTTP_ORIGIN"])) {
            return;
        }
        $origin = $_SERVER["HTTP_ORIGIN"];
        if (\in_array($origin, $domainConfig["allow_domains"])) {
            header("Access-Control-Allow-Origin:$origin");
            header("Access-Control-Allow-Headers:" . implode(",", $domainConfig["allow_headers"]));
            header("Access-Control-Allow-Methods:" . implode(",", $domainConfig["allow_methods"]));
            // ff($domainConfig['enable_cookie']);
            header("Access-Control-Allow-Credentials:" . $domainConfig["enable_cookie"] ?: "false");
            if ($domainConfig["expose_headers"] && is_array($domainConfig["expose_header"])) {
                foreach ($domainConfig["expose_headers"] as $expose_header) {
                    header("Access-Control-Expose-Header: $expose_header");
                }
            }
        }
    }

    public static function createKey()
    {
        $body = 'secret_key' . '+' . time();
        return $body;
    }

    public static function signToken()
    {
        $body = base64_encode(urlencode(self::createKey()));
        return base64_encode(mt_rand(100, 1000)) . '.' . $body . '==';
    }

    public static function getAccessKey()
    {
        return self::app()->getAccessKey();
    }

    /**
     * Create access token.
     *
     * @param array $data
     * @param string $accessKey
     * @return void
     */
    public function createAccessToken(array $data, string $accessKey = '')
    {
        if (self::$accessToken) {
            return self::$accessToken;
        }
        $body = '';
        if (!$accessKey) {
            $accessKey = self::getAccessKey();
        }
        foreach ($data as $item) {
            $body .= $item;
        }
        $body = trim($body);
        $bodyEncode = Encrypt::hash('sha256', '__BODY' . $body . 'BODY__');
        $accessToken = Encrypt::hash('sha256', '__CUBE_ACCESS_TOKEN' . $bodyEncode . 'CUBE_ACCESS_TOKEN__');
        self::$accessToken = $accessToken;
        return self::$accessToken;
    }

    /**
     * Verify access token.
     *
     * @param [type] $requestToken
     * @param [type] $data
     * @return void
     */
    public static function verifyAccessToken($requestToken, $data)
    {
        return $requestToken == self::createAccessToken($data);
    }

}
