<?php

namespace Ddm\ErrorHandler;

use \CakeLog;
use \Configure;
use \Debugger;

/**
 * Error handler triggering the default routines that normally occur in CakePHP
 * 1.3.x apps:
 * If debug is on:  Debugger::handleError()
 * If debug is off: CakeLog::handleError()
 *
 * This will also define the constant 'DISABLE_DEFAULT_ERROR_HANDLING' if not
 * defined already to take control of the error hanlding
 */
class CakeOneHandler extends ErrorHandler {

	public function __construct() {
		if (!defined('DISABLE_DEFAULT_ERROR_HANDLING')) {
			define('DISABLE_DEFAULT_ERROR_HANDLING', true);
		}
		$this->addErrorHandler([$this, 'logError']);
		$this->addExceptionHandler([$this, 'logException']);
	}

	public function logError($self, $params, $chain) {
		extract($params);
		if ($error && class_exists('CakeLog')) {
			extract($error);
			if (Configure::read('debug')) {
				Debugger::getInstance()->handleError($code, $message, $file, $line, $context);
			} else {
				CakeLog::getInstance()->handleError($code, $message, $file, $line, $context);
			}
			//prevent the default handler cake presents the error as required
			$params['default'] = false;
		}
		return $chain($params);
	}

	public function logException($self, $params, $chain) {
		extract($params);
		if ($exception && class_exists('CakeLog')) {
			$message = sprintf("[%s] %s",
				get_class($exception),
				$exception->getMessage()
			);
			$file = $exception->getFile();
			$line = $exception->getLine();
			if (Configure::read('debug')) {
				Debugger::getInstance()->handleError(E_ERROR, $message, $file, $line);
			} else {
				CakeLog::getInstance()->handleError(E_ERROR, $message, $file, $line);
			}
		}
		return $chain($params);
	}
}