<?php
namespace Daytalytics\Module;

use Daytalytics\WebInformationApi;
use Zend\Diactoros\ServerRequestFactory;
use Daytalytics\Error\WebErrorHandler;
use Exception;

class BatchRequest extends BaseModule {

    public function handle_request(array $params = []) {
        $WebInformationApi = WebInformationApi::get_instance();
        $module_name = false;
        $requests = $params['requests'];
        unset($params['requests']);
        if (is_string($requests)) {
            $requestString = $requests;
            $requests = [];
            parse_str($requestString, $requests);
            if (isset($requests['requests'])) {
                $requests = $requests['requests'];
            }
        }
        $mergeParams = $params;
        unset($mergeParams['data_source']);
        foreach($requests as $key => $request) {
            // Combine contant parameters like auth_token into the individual requests.
            // This also allows common parameter and overwriting
            //e.g.
            //?keyword=ipod
            //&request[1][module]=google_search&request[1][type]=ns
            //&request[2][module]=google_search&request[1][type]=ps
            $requests[$key] = array_merge($mergeParams, $request);
            // Using the first request as a standard, remove any that don't use the same
            // module.
            if ($module_name === false) {
                if (isset($request['module'])) {
                    $module_name = $request['module'];
                }
            } elseif ($module_name !== $request['module']) {
                unset($requests[$key]);
            }
        }
        
        //Run each request
        $results = array();
        $count = 0; $maximum = 100;
        foreach($requests as $key => $request) {
            if (++$count > $maximum) {
                break;
            }
            $request_object = ServerRequestFactory::fromGlobals($_SERVER, $request, $_POST, $_COOKIE, $_FILES);
            try {
                $WebInformationApi->validateRequestParams($request_object, true);
                $results[$key] = $WebInformationApi->handleRequest($request_object);
            } catch (Exception $exception) {
                $results[$key] = WebErrorHandler::formatExceptionOutput($exception);
            }   
        }
        return $results;
    }

    public function define_service() {
        return [
            'default' => [
                'parameters' => [
                    'requests' => [
                        'descriptiopn' => 'Requests array for batch operations, e.g. &requests[0][module]=google_search&requests[0][type]=ns&requests[0][keyword]=ipod',
                        'type' => 'array',
                        'required' => true,
                        'items' => [
                            'module' => [
                                'type' => 'string',
                                'required' => true,
                                'description' => 'Module for each batch operation (all must be for the same module)'
                            ],
                            'type' => [
                                'type' => 'string',
                                'required' => false,
                                'description' => 'Module service type for each batch operation'
                            ],
                            '_n' => [
                                'description' => 'Inidivual input param(s) for the service'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}