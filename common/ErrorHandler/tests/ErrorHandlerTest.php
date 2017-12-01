<?php

namespace Ddm\ErrorHandler\Test;

use Ddm\ErrorHandler\ErrorHandler;
use Exception;
use ReflectionClass;

/**
 * Class ErrorHandlerTest
 *
 * @package Ddm\ErrorHandler\Test
 * @coversDefaultClass Ddm\ErrorHandler\ErrorHandler
 */
class ErrorHandlerTest extends \PHPUnit_Framework_TestCase
{

    public static $errorReporting;

    public static function setUpBeforeClass()
    {
        self::$errorReporting = error_reporting(E_ALL);
    }

    public static function tearDownAfterClass()
    {
        error_reporting(self::$errorReporting);
    }


    public function tearDown()
    {
        ErrorHandler::restore();
    }

    /**
     * @test
     * @covers ::getInstance
     */
    public function getInstance()
    {
        $instance = ErrorHandler::getInstance();
        $this->assertEquals($instance, ErrorHandler::getInstance());
    }

    /**
     * @test
     * @covers ::register
     */
    public function register()
    {
        $stub = $this->getMockBuilder('Ddm\ErrorHandler\ErrorHandler');
        $stub->setMethods(['registerErrorHandler', 'registerExceptionHandler', 'registerFatalHandler', 'error_reporting']);
        /**
         * @var ErrorHandler $mockHandler
         */
        $mockHandler = $stub->getMock();

        $mockHandler->expects($this->once())
            ->method('registerErrorHandler');

        $mockHandler->expects($this->once())
            ->method('registerExceptionHandler');

        $mockHandler->expects($this->once())
            ->method('registerFatalHandler');

        $mockHandler->expects($this->once())
            ->method('error_reporting')
            ->with($this->equalTo(E_USER_DEPRECATED));

        $options = ['reporting' => E_USER_DEPRECATED];

        ErrorHandler::register($options, $mockHandler);
    }

    /**
     * @test
     * @covers ::restore
     */
    public function restore()
    {
        $stub = $this->getMockBuilder('Ddm\ErrorHandler\ErrorHandler');
        $stub->setMethods(['restoreErrorHandler', 'restoreExceptionHandler', 'restoreFatalHandler']);
        /**
         * @var ErrorHandler $mockHandler
         */
        $mockHandler = $stub->getMock();

        $mockHandler->expects($this->once())
            ->method('restoreErrorHandler');

        $mockHandler->expects($this->once())
            ->method('restoreExceptionHandler');

        $mockHandler->expects($this->once())
            ->method('restoreFatalHandler');

        ErrorHandler::restore($mockHandler);
    }

    /**
     * @test
     * @covers ::registerErrorHandler
     */
    public function registerErrorHandler() {
        $errorLevel = E_USER_NOTICE;
        $errorMessage = 'Test error';

        $stub = $this->getMockBuilder('Ddm\ErrorHandler\ErrorHandler');
        $stub->setMethods(['restoreErrorHandler', 'handleError']);
        /**
         * @var ErrorHandler $mockHandler
         */
        $mockHandler = $stub->getMock();

        $mockHandler->expects($this->once())
            ->method('restoreErrorHandler');

        $mockHandler->expects($this->once())
            ->method('handleError')
            ->with($this->equalTo($errorLevel), $this->equalTo($errorMessage));

        $mockHandler->registerErrorHandler($errorLevel, true);

        trigger_error($errorMessage, $errorLevel);

        $this->assertEquals(set_error_handler(function(){}), [$mockHandler, 'handleError']);
    }

    /**
     * @test
     * @covers ::registerFatalHandler
     */
    public function registerFatalHandler()
    {
        $stub = $this->getMockBuilder('Ddm\ErrorHandler\ErrorHandler');
        $stub->setMethods(['register_shutdown_function']);
        /**
         * @var ErrorHandler $mockHandler
         */
        $mockHandler = $stub->getMock();

        $mockHandler->expects($this->once())
            ->method('register_shutdown_function');

        $mockHandler->registerFatalHandler();

        //a second should not register the shutdown function again
        $mockHandler->registerFatalHandler();
    }

    /**
     * @test
     * @covers ::registerExceptionHandler
     */
    public function registerExceptionHandler()
    {
        $stub = $this->getMockBuilder('Ddm\ErrorHandler\ErrorHandler');
        $stub->setMethods(['restoreExceptionHandler']);
        /**
         * @var ErrorHandler $mockHandler
         */
        $mockHandler = $stub->getMock();

        $mockHandler->expects($this->once())
            ->method('restoreExceptionHandler');

        $mockHandler->registerExceptionHandler(true);

        $this->assertEquals(set_exception_handler(function(){}), [$mockHandler, 'handleException']);
    }

    /**
     * @test
     * @covers ::restoreErrorHandler
     */
    public function restoreErrorHandler()
    {
        $errorLevel = E_USER_NOTICE;
        $errorHandler = function(){};

        $stub = $this->getMockBuilder('Ddm\ErrorHandler\ErrorHandler');
        $stub->setMethods();
        /**
         * @var ErrorHandler $mockHandler
         */
        $mockHandler = $stub->getMock();

        set_error_handler($errorHandler, $errorLevel);

        $mockHandler->registerErrorHandler($errorLevel);

        $mockHandler->restoreErrorHandler();

        $this->assertEquals(set_error_handler(function(){}), $errorHandler);
    }

    /**
     * @test
     * @covers ::restoreFatalHandler
     */
    public function restoreFatalHandler()
    {
        $stub = $this->getMockBuilder('Ddm\ErrorHandler\ErrorHandler');
        $stub->setMethods(['register_shutdown_function']);
        /**
         * @var ErrorHandler $mockHandler
         */
        $mockHandler = $stub->getMock();

        $mockHandler->expects($this->once())
            ->method('register_shutdown_function');

        $mockHandler->registerFatalHandler();

        //Because shutdown function stats registered, we simply assert the precondition for calling it
        $class = new ReflectionClass($mockHandler);
        $property = $class->getProperty('handleFatals');
        $property->setAccessible(true);
        $handleFatals = $property->getValue($mockHandler);

        $this->assertNotEmpty($handleFatals);

        $mockHandler->restoreFatalHandler();

        $handleFatals = $property->getValue($mockHandler);

        $this->assertEmpty($handleFatals);
    }

    /**
     * @test
     * @covers ::restoreExceptionHandler
     */
    public function restoreExceptionHandler()
    {
        $exceptionHandler = function(){};

        $stub = $this->getMockBuilder('Ddm\ErrorHandler\ErrorHandler');
        $stub->setMethods();
        /**
         * @var ErrorHandler $mockHandler
         */
        $mockHandler = $stub->getMock();

        set_exception_handler($exceptionHandler);

        $mockHandler->registerExceptionHandler($exceptionHandler);

        $mockHandler->restoreExceptionHandler();

        $this->assertEquals(set_exception_handler(function(){}), $exceptionHandler);
    }

    /**
     * @test
     * @covers ::handleError
     */
    public function handleError()
    {
        $errorLevel = E_USER_NOTICE;
        $errorMessage = 'Test error';
        $error = [
            'code' => $errorLevel,
            'message' => $errorMessage,
            'file' => null,
            'line' => null,
            'context' => null
        ];

        $stub = $this->getMockBuilder('Ddm\ErrorHandler\ErrorHandler');
        $stub->setMethods(['filterMethod']);
        /**
         * @var ErrorHandler $mockHandler
         */
        $mockHandler = $stub->getMock();

        $mockHandler->expects($this->once())
            ->method('filterMethod')
            ->with($this->equalTo('handleError'), $this->equalTo([
                'error' => $error,
                'previous' => null,
                'default' => false
            ]));

        $mockHandler->registerErrorHandler($errorLevel);

        $mockHandler->handleError($errorLevel, $errorMessage);
    }

    /**
     * @test
     * @covers ::handleError
     */
    public function handleErrorPrevious()
    {
        $errorLevel = E_USER_NOTICE;
        $errorMessage = 'Test error';
        $error = [
            'code' => $errorLevel,
            'message' => $errorMessage,
            'file' => null,
            'line' => null,
            'context' => null
        ];

        $stub = $this->getMockBuilder('Ddm\ErrorHandler\ErrorHandler');
        $stub->setMethods(['error_reporting']);
        /**
         * @var ErrorHandler $mockHandler
         */
        $mockHandler = $stub->getMock();

        $stub = $this->getMockBuilder('StdClass');
        $stub->setMethods(['mockErrorHandler']);
        $mockProvider = $stub->getMock();

        $mockProvider->expects($this->once())
            ->method('mockErrorHandler')
            ->with($this->equalTo($error['code']), $this->equalTo($error['message']));

        $mockHandler->expects($this->any())
            ->method('error_reporting')
            ->will($this->returnValue(0));

        $mockHandler->registerErrorHandler($errorLevel);

        $mockHandler->handleError($errorLevel, $errorMessage);

        //Because we need to fake a previous existing handler
        $class = new ReflectionClass($mockHandler);
        $property = $class->getProperty('previousErrorHandler');
        $property->setAccessible(true);
        $property->setValue($mockHandler, [$mockProvider, 'mockErrorHandler']);

        $property = $class->getProperty('previousErrorLevel');
        $property->setAccessible(true);
        $property->setValue($mockHandler, $errorLevel);

        $mockHandler->handleError($errorLevel, $errorMessage);
    }

    /**
     * @test
     * @covers ::handleError
     */
    public function handleErrorInternals()
    {
        $errorLevel = E_USER_NOTICE;
        $errorMessage = 'Test error';
        $error = [
            'code' => $errorLevel,
            'message' => $errorMessage,
            'file' => null,
            'line' => null,
            'context' => null
        ];

        $stub = $this->getMockBuilder('Ddm\ErrorHandler\ErrorHandler');
        $stub->setMethods();
        /**
         * @var ErrorHandler $mockHandler
         */
        $mockHandler = $stub->getMock();

        $stub = $this->getMockBuilder('StdClass');
        $stub->setMethods(['mockErrorHandler']);
        $mockProvider = $stub->getMock();

        $mockProvider->expects($this->once())
            ->method('mockErrorHandler')
            ->with($this->equalTo($error['code']), $this->equalTo($error['message']));

        $mockHandler->registerErrorHandler($errorLevel);

        //Because we need to fake a previous existing handler
        $class = new ReflectionClass($mockHandler);
        $property = $class->getProperty('previousErrorHandler');
        $property->setAccessible(true);
        $property->setValue($mockHandler, [$mockProvider, 'mockErrorHandler']);

        $property = $class->getProperty('previousErrorLevel');
        $property->setAccessible(true);
        $property->setValue($mockHandler, $errorLevel);

        //mock default setting handler
        $mockHandler->addErrorHandler(function($self, $params, $chain){
            $params['default'] = true;
            return $chain($params);
        });

        $result = $mockHandler->handleError($errorLevel, $errorMessage);

        $this->assertFalse($result);
    }

    /**
     * @test
     * @covers ::handleFatalError
     */
    public function handleFatalError()
    {
        $errorLevel = E_USER_ERROR;
        $errorMessage = 'Test error';
        $error = [
            'type' => $errorLevel,
            'message' => $errorMessage,
            'file' => null,
            'line' => null,
            'context' => null
        ];

        $stub = $this->getMockBuilder('Ddm\ErrorHandler\ErrorHandler');
        $stub->setMethods(['filterMethod', 'error_get_last']);
        /**
         * @var ErrorHandler $mockHandler
         */
        $mockHandler = $stub->getMock();

        $mockHandler->expects($this->once())
            ->method('error_get_last')
            ->will($this->returnValue($error));

        $mockHandler->expects($this->once())
            ->method('filterMethod')
            ->with($this->equalTo('handleFatalError'), $this->equalTo([
                'error' => $error + ['code' => 0],
            ]));

        $mockHandler->registerFatalHandler();

        $mockHandler->handleFatalError();
    }

    /**
     * @test
     * @covers ::handleFatalError
     */
    public function handleFatalErrorExcluded()
    {
        $errorLevel = E_USER_NOTICE;
        $errorMessage = 'Test error';
        $error = [
            'type' => $errorLevel,
            'message' => $errorMessage,
            'file' => null,
            'line' => null,
            'context' => null
        ];

        $stub = $this->getMockBuilder('Ddm\ErrorHandler\ErrorHandler');
        $stub->setMethods(['error_get_last']);
        /**
         * @var ErrorHandler $mockHandler
         */
        $mockHandler = $stub->getMock();

        $mockHandler->expects($this->exactly(2))
            ->method('error_get_last')
            ->will($this->onConsecutiveCalls(null, $error));

        //not handled
        $mockHandler->handleFatalError();

        $mockHandler->registerFatalHandler();

        //no error
        $mockHandler->handleFatalError();

        //error not fatal
        $mockHandler->handleFatalError();
    }

    /**
     * @test
     * @covers ::handleFatalError
     */
    public function handleFatalErrorInternals()
    {
        $errorLevel = E_USER_ERROR;
        $errorMessage = 'Test error';
        $error = [
            'type' => $errorLevel,
            'message' => $errorMessage,
            'file' => null,
            'line' => null,
            'context' => null
        ];

        $stub = $this->getMockBuilder('Ddm\ErrorHandler\ErrorHandler');
        $stub->setMethods(['error_get_last', 'handleException']);
        /**
         * @var ErrorHandler $mockHandler
         */
        $mockHandler = $stub->getMock();

        $mockHandler->expects($this->once())
            ->method('error_get_last')
            ->will($this->returnValue($error));

        $mockHandler->expects($this->once())
            ->method('handleException')
            ->with($this->callback(function($subject){
                return is_a($subject,'\ErrorException');
            }));

        $mockHandler->registerFatalHandler();

        //mock throw setting handler
        $mockHandler->addFatalHandler(function($self, $params, $chain){
            return $chain($params);
        });

        $mockHandler->handleFatalError();
    }

    /**
     * @test
     * @covers ::handleException
     */
    public function handleException()
    {
        $errorLevel = E_USER_NOTICE;
        $errorMessage = 'Test error';
        $exception = new Exception($errorMessage, $errorLevel);

        $stub = $this->getMockBuilder('Ddm\ErrorHandler\ErrorHandler');
        $stub->setMethods(['filterMethod']);
        /**
         * @var ErrorHandler $mockHandler
         */
        $mockHandler = $stub->getMock();

        $mockHandler->expects($this->once())
            ->method('filterMethod')
            ->with($this->equalTo('handleException'), $this->equalTo([
                'exit' => false,
                'throw' => false,
                'exception' => $exception,
                'previous' => null,
            ]));

        $mockHandler->registerExceptionHandler();

        $mockHandler->handleException($exception);
    }

    /**
     * @covers ::handleException
     * @test
     */
    public function handleExceptionInternals() {
        $errorLevel = E_USER_NOTICE;
        $errorMessage = 'Test error';
        $exception = new Exception($errorMessage, $errorLevel);

        $stub = $this->getMockBuilder('Ddm\ErrorHandler\ErrorHandler');
        $stub->setMethods(['throw_', 'exit_']);
        /**
         * @var ErrorHandler $mockHandler
         */
        $mockHandler = $stub->getMock();

        $stub = $this->getMockBuilder('StdClass');
        $stub->setMethods(['mockExceptionHandler']);
        $mockProvider = $stub->getMock();

        $mockProvider->expects($this->once())
            ->method('mockExceptionHandler')
            ->with($this->equalTo($exception));

        $mockHandler->expects($this->exactly(2))
            ->method('throw_')
            ->with($this->equalTo($exception));

        $mockHandler->expects($this->once())
            ->method('exit_');

        $mockHandler->registerExceptionHandler(true);

        //Because we need to fake a previous existing handler
        $class = new ReflectionClass($mockHandler);
        $property = $class->getProperty('previousExceptionHandler');
        $property->setAccessible(true);
        $property->setValue($mockHandler, [$mockProvider, 'mockExceptionHandler']);

        //mock throw setting handler
        $mockHandler->addExceptionHandler(function($self, $params, $chain){
            $params['throw'] = true;
            return $chain($params);
        });

        //mock exit setting handler
        $mockHandler->addExceptionHandler(function($self, $params, $chain){
            $params['exit'] = true;
            return $chain($params);
        });

        //mock exception recursion handler
        $mockHandler->addExceptionHandler(function($self, $params, $chain){
            $self->handleException($params['exception']);
            return $chain($params);
        });

        $mockHandler->handleException($exception);
    }

    /**
     * @test
     * @covers ::addErrorHandler
     */
    public function addErrorHandler()
    {
        $errorLevel = E_USER_NOTICE;
        $errorMessage = 'Test error';
        $error = [
            'code' => $errorLevel,
            'message' => $errorMessage,
            'file' => null,
            'line' => null,
            'context' => null
        ];

        $stub = $this->getMockBuilder('Ddm\ErrorHandler\ErrorHandler');
        $stub->setMethods();
        /**
         * @var ErrorHandler $mockHandler
         */
        $mockHandler = $stub->getMock();

        $stub = $this->getMockBuilder('StdClass');
        $stub->setMethods(['handleError']);
        $mockProvider = $stub->getMock();

        $mockProvider->expects($this->once())
            ->method('handleError')
            ->with($this->equalTo($mockHandler), $this->equalTo([
                'error' => $error,
                'previous' => null,
                'default' => false
            ]));

        $mockHandler->registerErrorHandler($errorLevel);

        $mockHandler->addErrorHandler([$mockProvider, 'handleError']);

        $mockHandler->handleError($errorLevel, $errorMessage);
    }

    /**
     * @todo
     * @test
     * @covers ::addFatalHandler
     */
    public function addFatalHandler()
    {
        $errorLevel = E_USER_ERROR;
        $errorMessage = 'Test error';
        $error = [
            'type' => $errorLevel,
            'message' => $errorMessage,
            'file' => null,
            'line' => null,
            'context' => null
        ];

        $stub = $this->getMockBuilder('Ddm\ErrorHandler\ErrorHandler');
        $stub->setMethods(['error_get_last']);
        /**
         * @var ErrorHandler $mockHandler
         */
        $mockHandler = $stub->getMock();

        $stub = $this->getMockBuilder('StdClass');
        $stub->setMethods(['handleFatalError']);
        $mockProvider = $stub->getMock();

        $mockProvider->expects($this->once())
            ->method('handleFatalError')
            ->with($this->equalTo($mockHandler), $this->equalTo([
                'error' => $error + ['code' => 0],
            ]));

        $mockHandler->expects($this->once())
            ->method('error_get_last')
            ->will($this->returnValue($error));


        $mockHandler->registerFatalHandler();

        $mockHandler->addFatalHandler([$mockProvider, 'handleFatalError']);

        $mockHandler->handleFatalError();
    }

    /**
     * @test
     * @covers ::addExceptionHandler
     */
    public function addExceptionHandler()
    {
        $errorLevel = E_USER_NOTICE;
        $errorMessage = 'Test error';
        $exception = new Exception($errorMessage, $errorLevel);

        $stub = $this->getMockBuilder('Ddm\ErrorHandler\ErrorHandler');
        $stub->setMethods();
        /**
         * @var ErrorHandler $mockHandler
         */
        $mockHandler = $stub->getMock();

        $stub = $this->getMockBuilder('StdClass');
        $stub->setMethods(['handleException']);
        $mockProvider = $stub->getMock();

        $mockProvider->expects($this->once())
            ->method('handleException')
            ->with($this->equalTo($mockHandler), $this->equalTo([
                'exit' => false,
                'throw' => false,
                'exception' => $exception,
                'previous' => null,
            ]));

        $mockHandler->registerExceptionHandler();

        $mockHandler->addExceptionHandler([$mockProvider, 'handleException']);

        $mockHandler->handleException($exception);
    }

    /**
     * @test
     * @covers ::removeErrorHandler
     */
    public function removeErrorHandler()
    {
        $errorLevel = E_USER_NOTICE;
        $errorMessage = 'Test error';
        $error = [
            'code' => $errorLevel,
            'message' => $errorMessage,
            'file' => null,
            'line' => null,
            'context' => null
        ];

        $stub = $this->getMockBuilder('Ddm\ErrorHandler\ErrorHandler');
        $stub->setMethods();
        /**
         * @var ErrorHandler $mockHandler
         */
        $mockHandler = $stub->getMock();

        $stub = $this->getMockBuilder('StdClass');
        $stub->setMethods(['handleError']);
        $mockProvider = $stub->getMock();

        $mockProvider->expects($this->once())
            ->method('handleError')
            ->with($this->equalTo($mockHandler), $this->equalTo([
                'error' => $error,
                'previous' => null,
                'default' => false
            ]));

        $mockHandler->registerErrorHandler($errorLevel);

        $mockHandler->addErrorHandler([$mockProvider, 'handleError']);

        $mockHandler->handleError($errorLevel, $errorMessage);

        $mockHandler->removeErrorHandler([$mockProvider, 'handleError']);

        $mockHandler->handleError($errorLevel, $errorMessage);
    }

    /**
     * @todo
     * @test
     * @covers ::removeFatalHandler
     */
    public function removeFatalHandler()
    {
        $errorLevel = E_USER_ERROR;
        $errorMessage = 'Test error';
        $error = [
            'type' => $errorLevel,
            'message' => $errorMessage,
            'file' => null,
            'line' => null,
            'context' => null
        ];

        $stub = $this->getMockBuilder('Ddm\ErrorHandler\ErrorHandler');
        $stub->setMethods(['error_get_last']);
        /**
         * @var ErrorHandler $mockHandler
         */
        $mockHandler = $stub->getMock();

        $stub = $this->getMockBuilder('StdClass');
        $stub->setMethods(['handleFatalError']);
        $mockProvider = $stub->getMock();

        $mockProvider->expects($this->once())
            ->method('handleFatalError')
            ->with($this->equalTo($mockHandler), $this->equalTo([
                'error' => $error + ['code' => 0],
            ]));

        $mockHandler->expects($this->exactly(2))
            ->method('error_get_last')
            ->will($this->returnValue($error));


        $mockHandler->registerFatalHandler();

        $mockHandler->addFatalHandler([$mockProvider, 'handleFatalError']);

        $mockHandler->handleFatalError();

        $mockHandler->removeFatalHandler([$mockProvider, 'handleFatalError']);

        $mockHandler->handleFatalError();
    }

    /**
     * @todo
     * @test
     * @covers ::removeExceptionHandler
     */
    public function removeExceptionHandler()
    {

        $errorLevel = E_USER_NOTICE;
        $errorMessage = 'Test error';
        $exception = new Exception($errorMessage, $errorLevel);

        $stub = $this->getMockBuilder('Ddm\ErrorHandler\ErrorHandler');
        $stub->setMethods();
        /**
         * @var ErrorHandler $mockHandler
         */
        $mockHandler = $stub->getMock();

        $stub = $this->getMockBuilder('StdClass');
        $stub->setMethods(['handleException']);
        $mockProvider = $stub->getMock();

        $mockProvider->expects($this->once())
            ->method('handleException')
            ->with($this->equalTo($mockHandler), $this->equalTo([
                'exit' => false,
                'throw' => false,
                'exception' => $exception,
                'previous' => null,
            ]));

        $mockHandler->registerExceptionHandler();

        $mockHandler->addExceptionHandler([$mockProvider, 'handleException']);

        $mockHandler->handleException($exception);

        $mockHandler->removeExceptionHandler([$mockProvider, 'handleException']);

        //Because the handler method is mocked, the chain doesn't get called, so we must force reset this
        $class = new ReflectionClass($mockHandler);
        $property = $class->getProperty("handlingException");
        $property->setAccessible(true);
        $property->setValue($mockHandler, false);

        $mockHandler->handleException($exception);
    }
}