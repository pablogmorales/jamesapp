<?php

namespace Daytalytics\Error;

use Ddm\ErrorHandler\ErrorHandler;
use Daytalytics\WebInformationApi;
use Daytalytics\RequestException;
use Daytalytics\ResponseCode;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Response;
use Exception;

class WebErrorHandler extends ErrorHandler {

    /**
     *
     * @var ServerRequest
     */
    protected $request = null;

    /**
     *
     * @param FormatterInterface $formatter
     * @param ResponseInterface $response
     * @param unknown $errorPage
     */
    public function __construct(ServerRequest $request) {
        $this->request = $request;
        $this->addExceptionHandler([$this, 'webExceptionHandler']);
    }

    /**
     * Render status page... status or 503.
     *
     * @param unknown $self
     * @param unknown $params
     * @param unknown $chain
     * @return mixed
     */
    public function webExceptionHandler($self, $params, $chain) {
        $callChain = true;
        if (!empty($params['exception'])) {
            if ($params['exception'] instanceOf RequestException) {
                //these are auth/request errors, we don't want them to bubble up the error hanlder chain
                $callChain = false;
            }
        }
        if ($callChain) {
            $chain->next($self, $params, $chain);
        }
        if (!empty($params['exception'])) {
            $this->renderException($params['exception']);
        }
    }

    /**
     *
     * @param Exception $exception
     */
    public function renderException(Exception $exception) {
        global $start_time;
        $format = WebInformationApi::getRequestFormat($this->request) ?: 'html';
        $output = static::formatExceptionOutput($exception);
        $response = WebInformationApi::generateResponse($output, $format);
        $emitter = new SapiEmitter();
        $emitter->emit($response->withHeader('X-Total-Runtime', (string) (microtime(true) - $start_time) ));
        exit();
    }
    
    /**
     * 
     * @param Exception $exception
     * @return array
     */
    public static function formatExceptionOutput(Exception $exception) {
        $debug = getenv('APP_DEBUG');
        if ($debug) {
            $message = $exception->getMessage();
            if ($exception instanceOf RequestException) {
                $code = $exception->getCode() ?: ResponseCode::BAD_REQUEST;
            } else {
                $code = $exception->getCode() ?: ResponseCode::SERVER_ERROR;
            }
            $output = [
                'status' => 'ERROR',
                'status_message' => $exception->getMessage(),
                'status_code' => $code,
                'error' => [
                    'type' => get_class($exception),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => array_map(function($trace){
                        unset($trace['args']);
                        return $trace;
                    }, $exception->getTrace())
                ]
            ];
        } else {
            if ($exception instanceOf RequestException) {
                $message = $exception->getMessage();
                $code = $exception->getCode() ?: ResponseCode::BAD_REQUEST;
            } else {
                $message = 'An internal error has occured.';
                $code = ResponseCode::SERVER_ERROR;
            }
            $output = [
	           'status' => 'ERROR',
	           'status_message' => $message,
	           'status_code' => $code
	        ];
        }
        return $output;
    }
}
