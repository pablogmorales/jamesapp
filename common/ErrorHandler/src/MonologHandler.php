<?php

namespace Ddm\ErrorHandler;

use Psr\Log\LogLevel;
use Monolog\Logger;

/**
 * Abstract error handler for using with monolog logging adapters
 *
 */
abstract class MonologHandler extends ErrorHandler {

	protected $logger;

	protected $channel = 'default';

	protected $errorLevelMap = [
		E_ERROR             => LogLevel::CRITICAL,
		E_WARNING           => LogLevel::WARNING,
		E_PARSE             => LogLevel::ALERT,
		E_NOTICE            => LogLevel::NOTICE,
		E_CORE_ERROR        => LogLevel::CRITICAL,
		E_CORE_WARNING      => LogLevel::WARNING,
		E_COMPILE_ERROR     => LogLevel::ALERT,
		E_COMPILE_WARNING   => LogLevel::WARNING,
		E_USER_ERROR        => LogLevel::ERROR,
		E_USER_WARNING      => LogLevel::WARNING,
		E_USER_NOTICE       => LogLevel::NOTICE,
		E_STRICT            => LogLevel::NOTICE,
		E_RECOVERABLE_ERROR => LogLevel::ERROR,
		E_DEPRECATED        => LogLevel::NOTICE,
		E_USER_DEPRECATED   => LogLevel::NOTICE,
	];

	public function __construct($channel = null) {
		if (is_string($channel)) {
			$this->channel = $channel;
		}
		$this->logger = new Logger($this->channel);
		$this->setupMonoLogHandlers();
		$this->addErrorHandler([$this, 'logErrorHandler']);
		$this->addFatalHandler([$this, 'logFatalErrorHandler']);
		$this->addExceptionHandler([$this, 'logExceptionHandler']);
	}

	abstract protected function setupMonoLogHandlers();

	public function logErrorHandler($self, $params, $chain) {
		extract($params);
		if ($error) {
			extract($error);
			$this->logError($code, $message, $file, $line, $context);
		}
		return $chain($params);
	}

	public function logFatalErrorHandler($self, $params, $chain) {
		extract($params);
		if ($error) {
			extract($error);
			$this->logFatalError($code ?: $type, $message, $file, $line);
		}
		$params['exception'] = false;
		return $chain($params);
	}

	public function logExceptionHandler($self, $params, $chain) {
		extract($params);
		if ($exception) {
			$this->logException($exception);
		}
		return $chain($params);
	}

	public function logError($code, $message, $file, $line, $context = null) {
		$level = isset($this->errorLevelMap[$code]) ? $this->errorLevelMap[$code] : LogLevel::CRITICAL;
		$this->logger->log(
			$level,
			static::codeToString($code) . ': ' . $message,
			compact('code', 'message', 'file', 'line', 'context')
		);
	}

	public function logFatalError($code, $message, $file, $line) {
		$this->logger->log(
			LogLevel::ALERT,
			'Fatal Error (' . static::codeToString($code).'): ' . $message,
			compact('code', 'message', 'file', 'line')
		);
	}

	public function logException($exception) {
		$this->logger->log(
			LogLevel::ERROR,
			sprintf('Uncaught Exception %s: "%s" at %s line %s', get_class($exception), $exception->getMessage(), $exception->getFile(), $exception->getLine()),
			compact('exception')
		);
	}

	protected function getLogDir() {
		if (defined('LOGS')) {
			return rtrim(LOGS, DIRECTORY_SEPARATOR);
		} elseif (defined('TMP')) {
			return rtrim(TMP, DIRECTORY_SEPARATOR);
		} else {
			return sys_get_temp_dir();
		}
	}

	protected static function codeToString($code) {
		switch ($code) {
			case E_ERROR:
				return 'E_ERROR';
			case E_WARNING:
				return 'E_WARNING';
			case E_PARSE:
				return 'E_PARSE';
			case E_NOTICE:
				return 'E_NOTICE';
			case E_CORE_ERROR:
				return 'E_CORE_ERROR';
			case E_CORE_WARNING:
				return 'E_CORE_WARNING';
			case E_COMPILE_ERROR:
				return 'E_COMPILE_ERROR';
			case E_COMPILE_WARNING:
				return 'E_COMPILE_WARNING';
			case E_USER_ERROR:
				return 'E_USER_ERROR';
			case E_USER_WARNING:
				return 'E_USER_WARNING';
			case E_USER_NOTICE:
				return 'E_USER_NOTICE';
			case E_STRICT:
				return 'E_STRICT';
			case E_RECOVERABLE_ERROR:
				return 'E_RECOVERABLE_ERROR';
			case E_DEPRECATED:
				return 'E_DEPRECATED';
			case E_USER_DEPRECATED:
				return 'E_USER_DEPRECATED';
		}
		return 'Unknown PHP error';
	}
}