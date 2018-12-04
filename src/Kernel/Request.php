<?php

namespace Handscube\Kernel;

use Handscube\Assistants\Cookie;
use Handscube\Kernel\Http\RequestHeader;
use Handscube\Kernel\Session;

/**
 * Class Request. [c] Handscube.
 * Extremly simple implementation of HTTP request.
 * You can handle it through calling handle function which was implementation with interface Cube.
 *
 * @author J.W. <email@email.com>
 */
class Request
{

    use \Handscube\Traits\UrlTrait;

    private static $isInstance = false;
    private static $instance = null;

    public $requestType = '';
    public $requestTime = '';

    public $header;

    public $_server = []; //$_SERVER.
    public $query = [];
    public $post = []; //$_POST.
    public $input = []; //raw input.
    public $request = []; //$_REQUEST
    public $raw_data; //STD_IN.

    public $module;
    public $controller;
    public $action;

    public $host; //Request Host.
    public $home; //Home page.
    public $uri; //Request Uri.
    public $url; //Request Url.
    public $protocol = 'http'; //Request schema.
    public $pathInfo = []; //Request path Param pathParams.

    public $session;

    public $CTRL_ALIST;

    static $url_exp = "http://www.test.com/CONTROLLER_NAME/CONTROLLER_ACTION/others?id=1&name=jim&vid=wefas3234dsasc9m";
    private $args;

    const type = "Common";

    public function __construct($url = '')
    {
        $this->session = new Session();
        $this->__boot();
        $this->__init();
    }

    /**
     * Reqeust boot.
     *
     * @return void
     */
    private function __boot()
    {
        $this->header = new RequestHeader($this->getHeaders());
        $this->_server = $_SERVER;
        $this->query = $_GET;
        $this->post = $_POST;
        $this->request = $_REQUEST;
        $this->raw_data = \file_get_contents("php://input", "r");
        $this->input = $this->parseRawInput($this->raw_data);
        $this->requestTime = time();
        $this->requestType = array_key_exists("_method", $this->post) ? strtolower($this->post["_method"]) : strtolower($_SERVER["REQUEST_METHOD"]);
    }

    /**
     * Request init function.
     */
    private function __init()
    {

        $this->host = $this->host ? $this->host : $_SERVER['HTTP_HOST'];
        $this->uri = $this->uri ? $this->uri : $_SERVER['REQUEST_URI'];
        $this->protocol = array_key_exists("REQUEST_SCHEME", $_SERVER) ? $_SERVER['REQUEST_SCHEME'] : 'http';
        $this->url = $this->protocol . '://' . $this->host . $this->uri;
        $this->home = $this->protocol . '://' . $this->host;
        // $this->parseUrl();
    }

    public function __get($property)
    {
        // return $this->getter($property);
    }

    public function __set($property, $value)
    {

    }

    /**
     * Getter
     *
     * @param [type] $property
     * @return void
     */
    // public function getter($property)
    // {
    //     if ($this->request[$property]) {
    //         return $this->request[$property];
    //     } elseif ($this->_server[strtoupper($property)]) {
    //         return $this->_server[strtoupper($property)];
    //     } else {
    //         throw new \Handscube\Kernel\Exceptions\NoticeException("Property $property can not find in " . __CLASS__);
    //     }

    // }

    /**
     * Parse raw input to array
     *
     * @param [type] $rawInput
     * @return void
     */
    public function parseRawInput($rawInput)
    {
        if ($rawInput) {
            $parsedInput = [];
            if ($this->inputIsRaw($rawInput)) {
                foreach (explode('&', $rawInput) as $rawItem) {
                    $rawItemArr = explode('=', $rawItem);
                    $parsedInput[\urldecode($rawItemArr[0])] = urldecode($rawItemArr[1]);
                }
                return $parsedInput;
            } else if ($this->inputIsJson($rawInput)) {
                return \json_decode($rawInput, true);
            } else {
                return [];
            }
        }
        return [];
    }

    /**
     * Check whether input is json or not.
     *
     * @param [type] $input
     * @return void
     */
    public function inputIsJson($input)
    {
        return null === \json_decode($input) ? false : true;
        // return \json_decode($input) ? true : false;
    }

    /**
     * Check whether input is raw or not.
     *
     * @param [type] $input
     * @return void
     */
    public function inputIsRaw($input)
    {
        if (is_string($input)) {
            return strpos($input, '&') === false ? false : true;
        }
        return false;
    }

    /**
     * Emit event.
     *
     * @param [type] $name
     * @param [type] $message
     * @param [type] $from
     * @return void
     */
    public static function emit($name, $message, $from = __CLASS__)
    {
        // return Event::emit($from, $name, $message);
    }

    /**
     * Handle to router.
     *
     * @return void
     */
    public function handleToRoute()
    {
        Route::parseRoute();
    }

    /**
     * Call action.
     *
     * @param string $pars
     * @return void
     */
    public function callAction($pars = '')
    {
        $request = self::parseRequest();
        $calledCtrl = mapNamespace($request['CONTROLLER']);
        Cube::makeMethod($calledCtrl, $request['METHOD']);
    }

    /**
     * Get reqeust method|type.
     *
     * @return void
     */
    public function getRequestType()
    {
        return $this->requestType;
    }

    /**
     * Get headers parameters.
     *
     * @return [array] headers
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

    /**
     * Get part of request by passing parameters.
     *
     * @param [string | array] $params
     * @return void
     */
    public function only($params)
    {
        if (is_array($params)) {
            foreach ($params as $param) {
                return isset($this->request[$params]) ? $this->request[$param] : $this->input[$param];
            }
        }
        // ff($this->input[$params]);
        return isset($this->request[$params]) ? $this->request[$params] : $this->input[$params];
    }

    /**
     * Get other request data except this data.
     *
     * @param [type] $params
     * @return void
     */
    public function except($params)
    {
        $request = array_merge($this->request, $this->input);
        if (is_array($params)) {
            foreach ($params as $param) {
                unset($request[$param]);
            }
            return $request;
        }
        unset($request[$params]);
        return $request;
    }

    /**
     * Get request value by filed name.
     *
     * @param [type] $params
     * @return void
     */
    public function input($params = '')
    {
        if ($params) {
            if (is_array($params)) {
                foreach ($params as $param) {
                    return $this->request[$param] ?: $this->input[$param];
                }
            }
            return isset($this->request[$params]) ? $this->request[$params] : $this->input[$params];
        } else {
            return array_merge($this->request, $this->input);
        }

    }

    public function cookie()
    {
        return Cookie::all();
    }

}
