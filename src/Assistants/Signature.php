<?php

namespace Handscube\Assistants;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;

/**
 * Class Signature.
 */

class Signature
{

    protected $jwt;

    protected $decrypter;

    public static $encrypter = [

    ];

    const SIGN_TYPES = [
        'jwt',
    ];

    public function __construct()
    {
        if (!$this->jwt) {
            $this->jwt = new Builder();
        }
        if (!$this->decrypter) {
            $this->decrypter = new Parser();
        }
        if (!isset(self::$encrypter['sha256'])) {
            self::$encrypter['sha256'] = new Sha256();
        }
    }

    public function instanceWith(string $type)
    {
        if (!in_array(self::SIGN_TYPES)) {
            return;
        }
        switch ($type) {
            case 'jwt':
                if (!$this->jwt) {
                    $this->jwt = new Builder();
                }
                return $this->jwt;
                break;
            default:
                if (!$this->jwt) {
                    $this->jwt = new Builder();
                    return $this->jwt;
                }
        }
    }

    public function setIss(string $issure)
    {
        $this->jwt->setIssuer($issure);
        return $this;
    }

    public function setAud(string $audience)
    {
        $this->jwt->setAudience($audience);
        return $this;
    }

    public function setId(string $id, bool $flag)
    {
        $this->jwt->setId($id, $flag);
        return $this;
    }

    public function created($time)
    {
        $this->jwt->setIssuedAt($time);
        return $this;
    }

    public function setNbf($time)
    {
        $this->jwt->setNotBefore($time);
        return $this;
    }

    public function expire($time)
    {
        $this->jwt->setExpiration($time);
        return $this;
    }

    public function set($name, $value)
    {
        $this->jwt->set($name, $value);
        return $this;
    }

    public function sign($encrypter, string $secertKey)
    {
        $this->jwt->sign($encrypter, $secertKey);
        return $this->jwt->getToken();
    }

    public function decrypt($token)
    {
        $parse = $this->decrypter->parse($token);
        return $parse->getClaims();
    }

}
