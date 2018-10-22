<?php

namespace Handscube\Kernel;

/**
 * Undocumented class
 */
class Request
{

    use \Handscube\Traits\UrlTrait;

    private static $isInstance = false;
    private static $instance = null;

    public $requestType = '';
    public $requestTime = '';

    public $_server = []; //$_SERVER;
    public $_get = [];
    public $_post = [];
    public $_request = []; //$_REQUEST
    public $_input;

    public $module;
    public $controller;
    public $action;

    public $host;
    public $uri; //Request Uri.
    public $url; //Request Url.
    public $query = []; //Request Query.
    public $protocol = 'http';
    public $pathParams = []; //Request path Param pathParams

    public $CTRL_ALIST;

    static $url_exp = "http://www.test.com/CONTROLLER_NAME/CONTROLLER_ACTION/others?id=1&name=jim&vid=wefas3234dsasc9m";
    private $args;

    public function __construct($url = '')
    {
        $this->__boot();
        $this->__init();
        // self::emit("instance",["status"=>"new Requet"]);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    private function __boot()
    {
        $this->_server = $_SERVER;
        $this->_get = $_GET;
        $this->_post = $_POST;
        $this->_request = $_REQUEST;
        $this->_input = \file_get_contents("php://input", "r");
        $this->requestTime = time();
        $this->requestType = array_key_exists("_method", $this->_post) ? strtolower($this->_post["_method"]) : strtolower($_SERVER["REQUEST_METHOD"]);
    }

    private function __init()
    {

        $this->host = $this->host ? $this->host : $_SERVER['HTTP_HOST'];
        $this->uri = $this->uri ? $this->uri : $_SERVER['REQUEST_URI'];
        $this->protocol = $_SERVER['REQUEST_SCHEME'] ? $_SERVER['REQUEST_SCHEME'] : 'http';
        $this->url = $this->protocol . '://' . $this->host . $this->uri;
    }

    public function __get($property)
    {

        return $this->getter($property);

    }

    public function __set($property, $value)
    {

    }

    public function getter($property)
    {
        if ($this->_request[$property]) {
            return $this->_request[$property];
        } elseif ($this->_server[strtoupper($property)]) {
            return $this->_server[strtoupper($property)];
        } else {
            throw new \Handscube\Kernel\Exceptions\NoticeException("Property $property can not find in " . __CLASS__);
        }

    }

    public static function test()
    {
        echo "Test from request.";
    }

    public static function emit($name, $message, $from = __CLASS__)
    {
        // return Event::emit($from, $name, $message);
    }

    public function handleToRoute()
    {
        Route::parseRoute();
    }

    /**
     * Undocumented function
     *
     * @param string $pars
     * @return void
     */
    public function callAction($pars = '')
    {

        $request = self::parseRequest();
        echo "[\$request] - Request->callAction [Request.php Line 113]\n";
        print_r($request);
        $calledCtrl = mapNamespace($request['CONTROLLER']);
        Cube::makeMethod($calledCtrl, $request['METHOD']);
        // exit();
        // $class = new $calledCtrl();
        // call_user_func_array(array($class, $request['METHOD']), $pars);

    }

    public function getRequestType()
    {
        return $this->requestType;
    }

    /**
     * get headers parameters.
     */
    public function getHeaders()
    {
        if (function_exists('getallheaders')) {
            return \getallheaders();
        }
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }

    public function __destruct()
    {
        echo "Request destruct!!!\n";
    }

}
