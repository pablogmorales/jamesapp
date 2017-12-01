<?php

namespace Ddm\ErrorHandler;

use Monolog\Handler\NewRelicHandler as NewRelicLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Ddm\ErrorHandler\Formatter\LimitedContextNormalizerFormatter;
use Ddm\ErrorHandler\Formatter\LimitedContextLineFormatter;

/**
 * Error handler to send warning level & greater errors through to New Relic
 */
class NewRelicHandler extends MonologHandler {

	protected function setupMonoLogHandlers() {
		$enabled = extension_loaded('newrelic');
		if ($enabled) {
			$handler = new NewRelicLogger(Logger::NOTICE);
			$handler->setFormatter(new LimitedContextNormalizerFormatter);
		} else {
			$stream = $this->getLogDir() . "/{$this->channel}.newrelic.log";
			$handler = new StreamHandler($stream, Logger::NOTICE);
			$handler->setFormatter(new LimitedContextLineFormatter);
		}
		$this->logger->pushHandler($handler);
	}
}