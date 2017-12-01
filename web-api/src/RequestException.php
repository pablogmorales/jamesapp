<?php

namespace Daytalytics;

use Exception;

class RequestException extends Exception {
	
	protected $other = null;
    
	protected $headers = null;

	public function __construct($message, $code = null) {
		if(is_numeric($code)) {
			return parent::__construct($message, $code);
		}
		if (func_num_args() > 1) {
			$args = func_get_args();
			$message = array_shift($args);
			$other = array();
			foreach($args as $arg) {
				if (is_string($arg))
					$other[] = $arg;
			}
			$this->other = $other;
		}
		parent::__construct($message);
	}
	
	public function getOther() {
		return !empty($this->other) ? $this->other : array();
	}

    public function setHeaders($headers){
        $this->headers = $headers;
        return $this->headers;
    }

    public function getHeaders(){
        return is_null($this->headers) ? false : $this->headers;
    }
}