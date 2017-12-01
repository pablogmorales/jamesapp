<?php
/**
 * Private cron (via web) front controller
 * 
 * This routine differs from the main front controller:
 *  - $_GET['time'] is set to the current time()
 *  - dipatchj is hanlded via Daytalytics\WebInformationApi::dispatchCronRequest()
 *  
 *  @see Daytalytics\WebInformationApi::dispatchCronRequest()
 */

$start_time = microtime(true);

session_write_close();

require_once dirname(__DIR__) . "/config/bootstrap.php";

$_GET['time'] = time();
$_GET['do_cron_caching'] = true;
$request = Zend\Diactoros\ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);

$errorHandler = new Daytalytics\Error\WebErrorHandler($request);
Daytalytics\Error\WebErrorHandler::registerHandlers([['instance' => $errorHandler]]);

try {
    $response = Daytalytics\WebInformationApi::dispatchCronRequest($request);
    $emitter = new Zend\Diactoros\Response\SapiEmitter();
    $emitter->emit($response->withHeader('X-Total-Runtime', (string) (microtime(true) - $start_time)));
} catch (Daytalytics\RequestException $exception) {
    //Avoid request exceptions going through the error handler
    $errorHandler->renderException($exception);
}