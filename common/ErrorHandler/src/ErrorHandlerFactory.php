<?php
namespace Ddm\ErrorHandler;

/**
 * DDM Error Handler
 *
 * Usage:
 * Default/basic
 *  - Registers the default handlers only (new relic)
 * Ddm\ErrorHandler\ErrorHandlerFactory::register()
 *
 * Additional Handlers
 *   - Registers the default handlers, and the CakeOneHandler
 * Ddm\ErrorHandler\ErrorHandlerFactory::register(['CakeOne' => []])
 *
 * Additional Handlers Only
 *   - Registers onyl the CakeOneHandler
 * Ddm\ErrorHandler\ErrorHandlerFactory::register(['CakeOne' => []], false)
 *
 * Your own handlers
 *  - Register your custom error handler classes
 * Ddm\ErrorHandler\ErrorHandlerFactory::register(['MyNamespace\MyCustomHandler' => []], false)
 */
class ErrorHandlerFactory {

	protected static $handlerMap = [
		'NewRelic' => 'Ddm\ErrorHandler\NewRelicHandler',
		'Cake' => 'Ddm\ErrorHandler\CakeHandler',
		'CakeOne' => 'Ddm\ErrorHandler\CakeOneHandler',
	];

	public static function register($handlers = [], $defaults = []) {
		if ($defaults !== false) {
			$defaults = $defaults ?: static::defaultHandlers();
			$_handlers = $handlers;
			$handlers = [];
			foreach ($defaults as $handler => $settings) {
				if (isset($_handlers[$handler])) {
					$settings = array_merge($settings, $_handlers[$handler]);
				}
				$handlers[$handler] = $settings;
			}
			$handlers+= $_handlers;
		}
		$first = true;
		foreach ($handlers as $handler => $options) {
			if (isset(static::$handlerMap[$handler])) {
				$handler = static::$handlerMap[$handler];
			}
            /**
             * @var ErrorHandler $instance
             */
			$instance = isset($options['instance']) ? $options['instance'] : new $handler;
			unset($options['instance']);
			if (!isset($options['level'])) {
				$options['level'] = E_ALL & ~E_STRICT & ~E_DEPRECATED;
			}
			$callPrevious = !$first;
			if (!isset($options['callPreviousErrorHandler'])) {
				$options['callPreviousErrorHandler'] = $callPrevious;
			}
			if (!isset($options['callPreviousExceptionHandler'])) {
				$options['callPreviousExceptionHandler'] = $callPrevious;
			}
			$instance::register($options, $instance);
			$first = false;
		}
	}

	protected static function defaultHandlers() {
		$defaultHandlers = [
			'NewRelic' => [
				'level' => E_ALL & ~E_STRICT & ~E_DEPRECATED,
				'reporting' => E_ALL & ~E_STRICT & ~E_DEPRECATED
			]
		];
		return $defaultHandlers;
	}
}