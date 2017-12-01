<?php

namespace Ddm\ErrorHandler;

use \Configure;
use \FatalErrorException;
use \InternalErrorException;
use \CakeLog;

/**
 * Error handler triggering the default routines that normally occur in CakePHP 2
 */
class CakeHandler extends ErrorHandler {

	protected $cakeErrorHandler;

	protected $cakeExceptionHandler;

	public function __construct($cakeErrorHandler = null, $cakeExceptionHandler = null) {
		if ($cakeErrorHandler) {
			$this->useCakeErrorHandler($cakeErrorHandler);
		}
		if ($cakeExceptionHandler) {
			$this->useCakeExceptionHandler($cakeExceptionHandler);
		}
		if ($this->cakeErrorHandler) {
			$this->addErrorHandler([$this, 'cakeError']);
		}
		if ($this->cakeExceptionHandler) {
			$this->addExceptionHandler([$this, 'cakeException']);
		}
		$this->addFatalHandler([$this, 'cakeFatalError']);
	}

	public function useCakeErrorHandler($cakeErrorHandler) {
		$this->cakeErrorHandler = $cakeErrorHandler;
	}

	public function useCakeExceptionHandler($cakeExceptionHandler) {
		$this->cakeExceptionHandler = $cakeExceptionHandler;
	}

	public function cakeError($self, $params, $chain) {
		extract($params);
		if ($error && $this->cakeErrorHandler && is_callable($this->cakeErrorHandler)) {
			extract($error);
			call_user_func($this->cakeErrorHandler, $code, $message, $file, $line, $context);
			//prevent the default handler cake presents the error as required
			$params['default'] = false;
		}
		return $chain($params);
	}

	public function cakeFatalError($self, $params, $chain) {
		extract($params);
		if ($error && class_exists('Configure')) {
			extract($error);
			if (ob_get_level()) {
				ob_end_clean();
			}
			$exceptionMessage = 'Fatal Error (' . $type . '): ' . $message . ' in [' . $file . ', line ' . $line . ']';
			$exception = new FatalErrorException($exceptionMessage, 500, $file, $line);
			if ($this->cakeExceptionHandler && is_callable($this->cakeExceptionHandler)) {
				call_user_func($this->cakeExceptionHandler, $exception);
				$params['exception'] = false;
			} else {
				$params['exception'] = $exception;
			}
		}
		return $chain($params);
	}

	public function cakeException($self, $params, $chain) {
		extract($params);
		if ($exception && $this->cakeExceptionHandler && is_callable($this->cakeExceptionHandler)) {
			call_user_func($this->cakeExceptionHandler, $exception);
		}
		if (ob_get_length()) {
			$output = ob_get_contents();
			ob_end_clean();
		}
		if (!empty($output)) {
			$params['exit'] = $output;
		} else {
			$params['exit'] = true;
		}
		$params['throw'] = false;
		return $chain($params);
	}
}

?>