<?php

namespace Daytalytics;

class DatabaseException extends \Exception {
	protected $other = null;
	function __construct($message) {
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
}