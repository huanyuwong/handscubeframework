<?php

namespace Handscube\Kernel\Request;


class Request {

	const __App_DIR__ = '/p/advanced/handscube.php';

	private static $isInstance = false;
	private static $instance = NULL;

	public $requestType = '';
	public $requestTime = '';

	public $_server = []; //$_SERVER;
	public $_get = [];
	public $_post = [];
	public $_request = []; //$_REQUEST

	public $module;
	public $controller;
	public $action;

	public $HOST;
	public $REQ_URI; //Request Uri.
	public $REQ_URL; //Request Url.
	public $REQ_QUERY = []; //Request Query.
	public $PROTOCOL = 'http';
	public $REQ_PATHPARS = []; //Request path Param

	public $CTRL_ALIST;

	static $url_exp = "http://www.test.com/CONTROLLER_NAME/CONTROLLER_ACTION/others?id=1&name=jim&vid=wefas3234dsasc9m";
	private $args;

	function __construct($url = '') {
		$this->__boot();
		$this->__init();
		// self::emit("instance",["status"=>"new Requet"]);
	}

	private function __boot() {
		$this->_server = $_SERVER;
		$this->_get = $_GET;
		$this->_post = $_POST;
		$this->_request = $_REQUEST;
	}

	private function __init() {

		$this->HOST = $this->HOST ? $this->HOST : $_SERVER['HTTP_HOST'];
		$this->REQ_URI = $this->REQ_URI ? $this->REQ_URI : $_SERVER['REQUEST_URI'];
		$this->PROTOCOL = $_SERVER['REQUEST_SCHEME'] ? $_SERVER['REQUEST_SCHEME'] : 'http';
		$this->REQ_URL = $this->PROTOCOL . '://' . $this->HOST . $this->REQ_URI;
	}

	function __get($property) {

		if ($_REQUEST[$property]) {
			return $_REQUEST[$property];
		} elseif ($_SERVER[strtoupper($property)]) {
			return $_SERVER[strtoupper($property)];
		} else {
			echo "call Non-existent property $property\n";
			exit();
		}

	}

	function __set($property, $value) {

	}

	static function test() {
		echo "Test from request.";
    }
    

	static function emit($name, $message, $from = __CLASS__) {
		return Event::emit($from, $name, $message);
	}

	/**
	 * @Param $url [String] e.g. http[s]://test.com:8081?id=1&name=jim
	 * @return [Array] Return parsed url arr.['HOST','METHOD','']
	 * @temp-var @matchs [Array]
	 * Array
	 *(
	 *    [0] => //test.com:8081?id=1&name=jim
	 *    [1] => //
	 *    [2] => test.com:8081?id=1&name=jim
	 *)
	 */
	function parseRequest($url = '') {

		$url = $url ? $url : $this->REQ_URL;
		echo "requestUri: " . $this->REQ_URI . "\n\n";
		foreach (Route::$routingTable as $route => $resource) {
			if (Route::compareRoute($this->REQ_URI, $route)) {
				echo "true\n";
			} else {
				echo "false\n";
			}
		}
		exit();
		//Parse Request;
		preg_match('/(\/\/)(.*)/i', $url, $matchs);
		$detail = [];
		// print_r($matchs);
		// $matchs[2] e.g. test.com?id=1&name=jim;
		if (strpos($matchs[2], '?') !== FALSE) {
			/**
			 * $detail = [ [0] => test.com:8081 [1] => id=1&name=jim ]
			 */
			$detail = explode('?', $matchs[2]);
			$query = explode('&', $detail[1]); // [0 => id=1;1 => name=jim]

			foreach ($query as $k => $v) {
				$splitArr = explode('=', $v);
				@$this->REQ_QUERY[$splitArr[0]] = $splitArr[1]; //REQ_QUERY [id=>1,name=>jim]
			}
			$this->REQ_QUERY = Arr::filter($this->REQ_QUERY); //Request query parameters
			unset($splitArr);
		} else {
			$detail[] = $matchs[2]; //$detail = [ [0] => test.com:8081 ]

		}
		//Remove 'www.' if it exists.
		preg_match('/(www\.)?(.*)/i', $detail[0], $matchs2);
		/*
			$mathcs2
			(
				[0] => test.com:8081/index/index/test
				[1] =>
				[2] => test.com:8081/index/index/test
		*/
		$result = explode('/', $matchs2[2]);
		// print_r($matchs2); //Array
		print_r($result);

		$this->HOST = $this->HOST ? $this->HOST : array_pop($matchs2);
		$this->module = $result[1] ? $result[1] : 'index';
		$this->controller = $result[2] ? $result[2] : 'index';
		$this->action = $result[3] ? $result[3] : 'index';

		for ($i = 4; $i <= count($result) - 1; $i++) {
			@$this->REQ_PATHPARS[] = $result[$i];
		}

		print_r($this->REQ_PATHPARS);
		return ['HOST' => $this->HOST, 'MODULE' => $this->module, 'CONTROLLER' => $this->controller, 'ACTION' => $this->action, 'PATH_PARS' => $this->REQ_PATHPARS, 'QUERY' => $this->REQ_QUERY];

	}

	function handleToRoute() {
		Route::parseRoute();
	}

	function parseRoute() {

	}

	function callAction($pars = '') {

		$request = self::parseRequest();
		echo "[\$request] - Request->callAction [Request.php Line 113]\n";
		print_r($request);
		$calledCtrl = mapNamespace($request['CONTROLLER']);
		Cube::makeMethod($calledCtrl, $request['METHOD']);
		// exit();
		// $class = new $calledCtrl();
		// call_user_func_array(array($class, $request['METHOD']), $pars);

	}

	/**
	 * 获取header参数
	 */
	function getHeaders() {
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

}