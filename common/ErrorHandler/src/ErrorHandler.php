<?php

namespace Ddm\ErrorHandler;

/**
 * DDM Error Handler
 *
 * For usage:
 * @see ErrorHandlerFactory
 */
class ErrorHandler {

    protected static $instance;

    protected static $fatals = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];

    protected $handleErrors = false;

    protected $handleErrorLevel;

    protected $handleFatals = [];

    protected $reservedFatalMemory = '';

    protected $handleExceptions = false;

    protected $registeredFatalHandler = false;

    protected $previousErrorHandler;

    protected $previousErrorLevel;

    protected $previousExceptionHandler;

    protected $handlingException = false;

    protected $methodFilters = [];

    public static function getInstance() {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public static function register($options = [], ErrorHandler $instance = null) {
        $instance = $instance ?: static::getInstance();
        $options += [
            'level' => E_ALL,
            'fatals' => [],
            'reserveFatalMemorySize' => 0,
            'callPreviousErrorHandler' => false,
            'callPreviousExceptionHandler' => false
        ];
        $instance->registerErrorHandler($options['level'], $options['callPreviousErrorHandler']);
        $instance->registerExceptionHandler($options['callPreviousExceptionHandler']);
        $instance->registerFatalHandler($options['fatals'], $options['reserveFatalMemorySize']);
        if (isset($options['reporting'])) {
            $instance->error_reporting($options['reporting']);
        }
        return $instance;
    }

    public static function restore(ErrorHandler $instance = null) {
        $instance = $instance ?: static::getInstance();
        $instance->restoreErrorHandler();
        $instance->restoreFatalHandler();
        $instance->restoreExceptionHandler();
        return $instance;
    }

    public function registerErrorHandler($level = E_ALL, $callPrevious = false) {
        $this->restoreErrorHandler();
        $this->handleErrorLevel = $level;
        $previousErrorHandler = $this->set_error_handler([$this, 'handleError'], $level);
        $this->handleErrors = true;
        if ($callPrevious) {
            $this->previousErrorLevel = is_int($callPrevious) ? $callPrevious : E_ALL;
            $this->previousErrorHandler = $previousErrorHandler;
        }
    }

    public function registerFatalHandler($fatals = [], $reserveMemorySize = 0) {
        $this->handleFatals = $fatals ?: static::$fatals;
        $this->reservedFatalMemory = str_repeat(' ', 1024 * $reserveMemorySize);
        if ($this->registeredFatalHandler === false) {
            $this->registeredFatalHandler = true;
            $this->register_shutdown_function([$this, 'handleFatalError']);
        }
    }

    public function registerExceptionHandler($callPrevious = false) {
        $this->restoreExceptionHandler();
        $previousExceptionHandler = $this->set_exception_handler([$this, 'handleException']);
        $this->handleExceptions = true;
        if ($callPrevious) {
            $this->previousExceptionHandler = $previousExceptionHandler;
        }
    }

    public function restoreErrorHandler() {
        if ($this->handleErrors) {
            $this->restore_error_handler();
            unset($this->handleErrorLevel, $this->previousErrorHandler, $this->previousErrorLevel);
            $this->handleErrors = false;
        }
    }

    public function restoreFatalHandler() {
        $this->handleFatals = [];
        $this->reservedFatalMemory = '';
    }

    public function restoreExceptionHandler() {
        if ($this->handleExceptions) {
            $this->restore_exception_handler();
            unset($this->previousExceptionHandler);
            $this->handleExceptions = false;
        }
    }

    public function handleError($code, $message, $file = null, $line = null, $context = null) {
        $previous = $this->previousErrorHandler;
        if ($this->error_reporting() === 0 || !($this->handleErrorLevel & $code)) {
            if ($previous && ($this->previousErrorLevel & $code)) {
                return call_user_func($previous, $code, $message, $file, $line, $context);
            }
            //errors suppressed or not hanlded
            return false;
        }
        $error = compact('code', 'message', 'file', 'line', 'context');
        $previous = $this->previousErrorHandler;
        $default = false;
        $params = compact('error', 'previous', 'default');
        return $this->filterMethod(__FUNCTION__, $params, function($self, $params){
            extract($params);
            /**
             * @var null | Closure $previous
             * @var array $error
             * @var boolean $default
             */
            if ($previous && ($self->previousErrorLevel & $error['code'])) {
                extract($error);
                /**
                 * @var number $code
                 * @var string $message
                 * @var string $file
                 * @var string $line
                 * @var string $context
                 */
                call_user_func($previous, $code, $message, $file, $line, $context);
            }
            if ($default) {
                //bool:false = let the default error handler receive it
                return false;
            }
        });
    }

    public function handleFatalError() {
        if (empty($this->handleFatals)) {
            return;
        }
        $error = $this->error_get_last();
        if (!is_array($error)) {
            return;
        }
        if (!in_array($error['type'], $this->handleFatals, true)) {
            return;
        }
        $error['code'] = 0;
        $params = compact('error');
        $this->filterMethod(__FUNCTION__, $params, function($self, $params){
            extract($params);
            /**
             * @var array $error
             */
            if (!isset($exception)) {
                $exception = new \ErrorException($error['message'], $error['code'], $error['type'], $error['file'], $error['line']);
            }
            if ($exception) {
                $self->handleException($exception);
            }
        });
    }

    public function handleException($exception) {
        if ($this->handlingException) {
            //encountered an exception, while handling an exception, BAIL OUT!
            return $this->throw_($exception);
        }
        $exit = $throw = false;
        $previous = $this->previousExceptionHandler;
        $params = compact('exit', 'throw', 'exception', 'previous');
        $this->handlingException = true;
        return $this->filterMethod(__FUNCTION__, $params, function($self, $params){
            extract($params);
            /**
             * @var null | Closure $previous
             * @var Exception $exception
             * @var boolean $exit
             * @var boolean $throw
             */
            if ($previous) {
                call_user_func($previous, $exception);
            }
            if ($exit) {
                $this->exit_($exit);
            }
            if ($exception && $throw) {
                $this->throw_($exception);
            }
            $self->handlingException = false;
        });
    }

    public function addErrorHandler($callable) {
        $this->addMethodFilter('handleError', $callable);
    }

    public function addFatalHandler($callable) {
        $this->addMethodFilter('handleFatalError', $callable);
    }

    public function addExceptionHandler($callable) {
        $this->addMethodFilter('handleException', $callable);
    }

    public function removeErrorHandler($callable) {
        $this->removeMethodFilter('handleError', $callable);
    }

    public function removeFatalHandler($callable) {
        $this->removeMethodFilter('handleFatalError', $callable);
    }

    public function removeExceptionHandler($callable) {
        $this->removeMethodFilter('handleException', $callable);
    }

    protected function addMethodFilter($method, $filter) {
        $this->methodFilters[$method][] = $filter;
    }

    protected function removeMethodFilter($method, $filter) {
        if ($filter === true) {
            $this->methodFilters[$method] = [];
        } elseif (!empty($this->methodFilters[$method])) {
            $index = array_search($filter, $this->methodFilters[$method]);
            if ($index !== false) {
                unset($this->methodFilters[$method][$index]);
            }
        }
    }

    protected function filterMethod($method, $params, $callback, $filters = array()) {
        $_filters = [];
        if (!empty($this->methodFilters[$method])) {
            $_filters = $this->methodFilters[$method];
        }
        if (empty($_filters) && empty($filters)) {
            return $callback($this, $params, null);
        }
        $filters = array_merge($_filters, $filters, [$callback]);
        $filter = new ErrorFilter($this, $method, $filters);
        return $filter->run($params);
    }

    /**
     * @see error_reporting()
     * @codeCoverageIgnore
     */
    protected function error_reporting($level = null) {
        if ($level === null) {
            return error_reporting();
        }
        return error_reporting($level);
    }

    /**
     * @see set_error_handler()
     * @codeCoverageIgnore
     */
    protected function set_error_handler($error_handler, $error_types = E_ALL | E_STRICT) {
        return set_error_handler($error_handler, $error_types);
    }

    /**
     * @see set_exception_handler()
     * @codeCoverageIgnore
     */
    protected function set_exception_handler($exception_handler) {
        return set_exception_handler($exception_handler);
    }

    /**
     * @see register_shutdown_function()
     * @codeCoverageIgnore
     */
    protected function register_shutdown_function($function, $parameter = null) {
        return register_shutdown_function($function, $parameter);
    }

    /**
     * @see restore_error_handler()
     * @codeCoverageIgnore
     */
    protected function restore_error_handler() {
        return restore_error_handler();
    }

    /**
     * @see restore_exception_handler()
     * @codeCoverageIgnore
     */
    protected function restore_exception_handler() {
        return restore_exception_handler();
    }

    /**
     * @see error_get_last()
     * @codeCoverageIgnore
     */
    protected function error_get_last() {
        return error_get_last();
    }

    /**
     * @see exit()
     * @codeCoverageIgnore
     */
    protected function exit_($status = null) {
        if ($status === null) {
            exit();
        }
        exit($status);
    }

    /**
     * @link http://php.net/manual/en/language.exceptions.php
     * @codeCoverageIgnore
     */
    protected function throw_($exception) {
        throw $exception;
    }

    /**
     * @deprecated
     * @see ErrorHandlerFactory::register()
     * @codeCoverageIgnore
     */
    public static function registerHandlers($handlers = [], $defaults = []) {
        ErrorHandlerFactory::register($handlers, $defaults);
    }
}