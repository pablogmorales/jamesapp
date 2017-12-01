<?php
/**
 * Public web front controller
 */

$start_time = microtime(true);

session_write_close();

require_once dirname(__DIR__) . "/config/bootstrap.php";

$request = Zend\Diactoros\ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);

$errorHandler = new Daytalytics\Error\WebErrorHandler($request);
Daytalytics\Error\WebErrorHandler::registerHandlers([['instance' => $errorHandler]]);

try {
    $response = Daytalytics\WebInformationApi::dispatchRequest($request);
    $emitter = new Zend\Diactoros\Response\SapiEmitter();
    $emitter->emit($response->withHeader('X-Total-Runtime', (string) (microtime(true) - $start_time)));
} catch (Daytalytics\RequestException $exception) {
    //Avoid request exceptions going through the error handler
    $errorHandler->renderException($exception);
}
