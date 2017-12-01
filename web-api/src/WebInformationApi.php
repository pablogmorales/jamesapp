<?php
namespace Daytalytics;

use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;
use Exception;
use ReflectionClass;

/**
 * WebInformationApi
 *
 */
class WebInformationApi {
	
	/**
	 *
	 * @var WebInformationApi WebInformationApi
	 */
	private static $singleton_instance;
	
	/**
	 * 
	 * @var Database Database
	 */
	public $db;
	
	/**
	 * 
	 * @var array
	 */
	protected static $contentTypes = [
		'text/html' => 'html', 
		'application/json' => 'json', 
		'application/xml' => 'xml', 
		'application/vnd.php-object' => 'php'
	];
	
	/**
	 * 
	 * @var array
	 */
	protected static $contentTypeFormatters = [
		'html' => ['Daytalytics\Response\Html', 'format'], 
		'json' => ['Daytalytics\Response\Json', 'format'], 
		'xml' => ['Daytalytics\Response\Xml', 'format'], 
		'php' => ['Daytalytics\Response\Serialize', 'format']
	];

	/**
	 * 
	 * @var string
	 */
	protected $info = 'Web Information API';

    /**
     * @var \Daytalytics\AuthToken
     */
    protected $authToken ;

	/**
	 * 
	 * @return \Daytalytics\WebInformationApi
	 */
	public static function get_instance() {
		if (!isset(self::$singleton_instance)) {
			self::$singleton_instance = new WebInformationApi(Database::get_instance());
		}
		return self::$singleton_instance;
	}

	/**
	 * 
	 * @param ServerRequest $request
	 * @return \Zend\Diactoros\Response
	 */
	public static function dispatchRequest(ServerRequest &$request) {
	    $WebInformationApi = static::get_instance();
        $WebInformationApi->validateRequest($request);
        $output = $WebInformationApi->handleRequest($request);
	    $format = $request->getAttribute('format');
	    $response = static::generateResponse($output, $format);
	    return $response;
	}
	
	/**
	 * Initiate cron request. Default content type is json
	 */
	public static function dispatchCronRequest(ServerRequest $request) {
	    $WebInformationApi = static::get_instance();
	    $format = static::$contentTypes['application/json'];
	    $request = $request->withAttribute('format', $format);
	    $WebInformationApi->validateCronRequest($request);
	    $output = $WebInformationApi->handleRequest($request);
	    $response = static::generateResponse($output, $format);
	    return $response;
	}
	
	
	public static function generateResponse($output, $format) {
	    $formatter = static::$contentTypeFormatters[$format];
	    $contentTypes = array_flip(static::$contentTypes);
	    $contentType = $contentTypes[$format];
	    $responseBody = call_user_func($formatter, $output);
	     
	    $responseCode = $output['status_code'];
	    if (empty($responseCode)) {
	        $responseCode = @$output['status'] == 'OK' ? 200 : 500;
	    }
	    $responseHeaders = ['Cache-Control' => 'no-store', 'Content-Type' => $contentType];
	    $body = new Stream('php://temp', 'wb+');
	    $body->write($responseBody);
	    $response = new Response($body, $responseCode, $responseHeaders);
	    return $response;
	}
	
	public static function getRequestFormat(ServerRequest $request) {
	    if ($format = $request->getAttribute('format')) {
	        return $format;
	    } else {
	        $accept = $request->getHeader('Accept');
	        $accept = reset($accept);
	        if(isset(static::$contentTypes[$accept])) {
	            return static::$contentTypes[$accept]; 
	        }
	    }
	}
	
	/**
	 * @name __construct()
	 * @description Setup the API.
	 */
	public function __construct(Database $db) {
	    $this->db = $db;
	}

	public function validateRequest(ServerRequest &$request) {
	    $this->validateRequestType($request);
	    $this->validateRequestAuth($request);
	    $this->validateRequestParams($request);
	}
	
	public function validateCronRequest(ServerRequest &$request) {
	    $this->validateCronRequestAuth($request);
	    $this->validateRequestParams($request, true, true);
	}
	
	public function validateRequestAuth(ServerRequest $request, $setAuth = true) {
	    //Auth handling
	    IpAuthorizer::fromRequest($request);
	    $auth = AuthToken::fromRequest($request);
	    if (!$auth || !$auth->enabled) {
	        throw new UnauthorizedRequestException("Invalid auth token.", 403);
	    }
	    $auth->validateRequestSignature();
	    if ($setAuth) {
	        $this->authToken = $auth;
	    }
	    return $auth;
	}
	
	public function validateCronRequestAuth(ServerRequest $request, $setAuth = true) {
	    //Auth handling
	    IpAuthorizer::fromRequest($request);
	    $auth = AuthToken::fromRequestQuery($request);
	    if (!$auth || !$auth->enabled) {
	        throw new UnauthorizedRequestException("Invalid auth token.", 403);
	    }
	    if ($setAuth) {
	        $this->authToken = $auth;
	    }
	    return $auth;
	}
	
	public function validateRequestParams(ServerRequest $request, $requireModule = false, $allowPrivate = false) {
	    $params = $request->getQueryParams();
	    //timestamp
	    if(!isset($params['time'])) {
	        $time = 0;
	    } else {
	        $time = (int)$params['time'];
	    }
	    if ($time < (time() - 2*60*60) || $time > (time() + 2*60*60)) {
	       throw new RequestException('Invalid timestamp', ResponseCode::BAD_REQUEST);
	    }
	    //module
	    if (isset($params['module'])) {
	        try {
	            $module = $this->get_module($params['module']);
	        } catch (Exception $e) {
	            throw new RequestException('Invalid module', ResponseCode::BAD_REQUEST);
	        }
	        $moduleClass = get_class($module);
	        if ($moduleClass::$private && !$allowPrivate) {
	            throw new RequestException('Invalid module', ResponseCode::BAD_REQUEST);
	        }
	    } elseif ($requireModule) {
	        throw new RequestException('Invalid module', ResponseCode::BAD_REQUEST);
	    }
	}
	
	public function validateRequestType(ServerRequest &$request) {
	    //Content type
	    if ($format = static::getRequestFormat($request)) {
	        $request = $request->withAttribute('format', $format);
	    } else {
	        throw new RequestException('Invalid content type.', ResponseCode::BAD_REQUEST);
	    }
	    //HTTP:GET only
	    if($request->getMethod() != 'GET') {
	        throw new RequestException('Method not allowed', 405);
	    }
	}
	
	
	/**
	 * @name handleRequest()
	 * @description Interpret the request and begin processing what it asks for.
	 * Fail if the request can't be processed.
	 * @returns the appropriate response that the request expects. e.g. search
	 * results.
	 * false on failure.
	 */
	public function handleRequest(ServerRequest $request) {
        $params = $request->getQueryParams();

		//!TODO: TEMPORARY: Allow 10 minutes for a request to process. This should be
	 	// updated to reflect the expected runtime once such data is available.
		set_time_limit(10*60);

	 	$output = [];
	 	$output['status'] = 'OK';
	 	$output['status_message'] = 'This was successful';
	 	$output['status_code'] = ResponseCode::OK;

	 	$do_results = 'show';
	 	if(!empty($params['results'])) {
	 		$do_results = strtolower($params['results']);
	 		unset($params['results']);
	 	}
	 	switch($do_results) {
	 		case 'none' :
	 			$do_results = false;
	 			break;
	 		case 'hide' :
	 			$do_results = 'hide';
	 			break;
	 		default :
	 		case 'show' :
	 			$do_results = 'show';
	 			break;
	 	}

	 	if(!empty($params['module'])) {
	 	    $module = $this->get_module($params['module']);
		 	$module_error = false;
		 	if(!empty($module)) {
		 		if ($do_results) {
			 		if ($do_results == "hide") {
		 				$module->show_results = false;
			 		}
 					$this->authToken->trackUsage($module, $params);
		 			$cacheResult = false;
		 			//if data source is local or not specified attempt to get local data prior to rate limiting
		 			if (empty($params['data_source']) || $params['data_source'] === 'local') {
			 			try {
			 				$_request = array('data_source' => 'local') + $params;
			 				$module_response = $module->__invoke($_request);
			 				$module_error = $module->get_error();
			 				$cacheResult = $module_error === false;
			 			} catch (Exception $e) {
							//failed to get local data
			 			}
		 			}
		 			
					if ($cacheResult === false) {
						RateLimiter::go($request, $this->authToken, $module);
						$module_response = $module->__invoke($params);
						$module_error = $module->get_error();
						if ($module_error !== false) {
							throw new RequestException($module_error);
						}
					}

		 			if ($do_results != 'hide') {
		 			    $output['results'] = $module_response;
		 				unset($module_response);
		 			}
		 		}
		 	}
	 	}
        
        if (getenv('APP_DEBUG')) {
            //include debug output
            $output['memory'] = memory_get_peak_usage(true);
            if (!empty($module)) {
            	$output['datasource'] = $module->get_final_datasource();
            	$output['module_info'][] = $module->identify(true);
            }
        }
	 	return $output;
	}

	public function get_module($module_identifier) {
		return ModuleRegistry::get($module_identifier);
	}
}
