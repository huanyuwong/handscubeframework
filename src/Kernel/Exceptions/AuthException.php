<?php

namespace Handscube\Kernel\Exceptions;

/**
 * InsideExceptions Class.
 */
class AuthException extends \Exception
{

    protected $errFile = '';
    protected $errLine = '';

    public function __construct($message = "", $code = 0, $previous = null, $errFile = "", $errLine = "")
    {
        parent::__construct($message, $code, $previous);
        $this->errFile = $errFile;
        $this->errLine = $errLine;
    }

    public function getErrorInfo($type = 2)
    {
        $err = [
            'errCode' => $this->getCode(),
            'errMsg' => $this->getMessage(),
            'errFile' => $this->getErrorFile(),
            'errLine' => $this->getErrorLine(),
        ];
        if ($type == 1) {
            return json_encode($err);
        }
        return $err;
    }

    public function getErrorFile()
    {
        return $this->errFile;
    }

    public function getErrorLine()
    {
        return $this->errLine;
    }

}
