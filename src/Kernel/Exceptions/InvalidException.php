<?php

namespace Handscube\Kernel\Exceptions;

/**
 * InsideExceptions Class.
 */
class InvalidException extends \Exception {

	protected $errFile = '';
	protected $errLine = '';

	function __construct($message = "", $code = 0, $previous = NULL, $errFile = "", $errLine = "") {
		parent::__construct($message, $code, $previous);
		$this->errFile = $errFile;
		$this->errLine = $errLine;
	}

	public function getErrorInfo($type = 2) {
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

	function getErrorFile() {
		return $this->errFile;
	}

	function getErrorLine() {
		return $this->errLine;
	}

}

?>