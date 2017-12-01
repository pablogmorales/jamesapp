<?php

namespace Daytalytics;

use Exception;


class UrlGetter {
	
	private $params = [];
	private $module;

	private $url;
	private $other_identifier;
	private $retry_options;
	private $current_proxy;

	private $proxy_request_options = [];

	const max_attempts = 5;

	public function __construct($module, $params) {
		$this->module = $module;
		$this->params = $params;
		$this->process_params($params);
	}

	public function get_request_url() {
		return $this->url;
	}
	
	private function request() {
		$this->current_proxy = $this->module->get_proxy();
		if(empty($this->current_proxy)) {
			throw new Exception('No suitable proxy found.');
		} elseif ($this->current_proxy['path'] != 'self') {
			$proxyRequest = new ProxyRequest($this->current_proxy, $this->params + $this->proxy_request_options);
			return $proxyRequest->getResponse($this->url);
		} else {
			$request = new BasicRequest($this->params);
			return $request->getResponse($this->url);
		}
	}
	
	public function get(&$info=array()) {
		$auth_token = false;

		// Catch exception so it won't get error when running unit tests
		try {
			$auth_token = AuthToken::find_from_request();
		} catch (Exception $e) {
			$auth_token = false;
		}
		
		// This is our alternative to no FOLLOWLOCATION/MAXREDIRS. Detect some basic redirects and apply then up to 5 loops
		// Also "try again" once if the response was false
		$headers = array();
		$error = false;
		$http_code = 0;
		$headers_out = '';
		$response = false;

        $max_attempts = self::max_attempts;

        if (isset($this->retry_options['max_attempts'])) {
            $max_attempts = $this->retry_options['max_attempts'];
        }

		for ($i=0; $i<$max_attempts; $i++) {

			//start the request
			$_response = $this->request();
			
			$response = $_response['response'];
			$http_code = $_response['http_code'];
			$errorNumber = $_response['error_number'];
			$headers = $_response['headers'];
			$error = $_response['error'];
			$headers_out = $_response['headers_out'];
			
			
			$on_last_attempt = $i >= self::max_attempts - 1;
			$shouldRedirect = $this->should_redirect($http_code) && !$this->is_post() && isset($headers['Location']);

			// Track failed attempts to get a page through the proxy.
			if ($this->handle_proxy_failure($http_code)){
				continue;
			}
            //Track usage
			$this->track_proxy_usage($http_code, null);
			
			// Attempt to request again if not redirecting return code is not 200 && 403
			if ((!$shouldRedirect && !$this->is_200($http_code) && !$this->is_403($http_code)) || $errorNumber == 28) {
				if(!$on_last_attempt) {
					continue;
				}
			}

			if ($this->is_400($http_code) || $this->is_500($http_code)) {
				if(!$on_last_attempt) {
					// Allow it to try again.
					continue;
				}
			} elseif ($shouldRedirect) {
				if (!$on_last_attempt) {
					//if an http response code in 300 comes back, do the redirect
					// @todo - what if this is a relative url? fix!!
					$this->url = $headers['Location'];
					continue;
				}
			} elseif (!$this->module->is_valid_response($response)) {
				$this->handleDataUnavailable($http_code, $headers, $response);
			} elseif ($this->module->should_retry_request($response, $this->retry_options)) {
				if (!$on_last_attempt){
				    continue;
				}
			} else {
				// In any good case, break to return the results.
				// In any other bad case, break to handle the lack of results.
				break;
			}
			//if we have invalid data on our last loop, then throw an exception
			$this->handleDataUnavailable($http_code, $headers, $response);
		}
		
		$info['headers'] = $headers;
		$info['error'] = $error;
		$info['headers_out'] = $headers_out;
		$info['http_code'] = $http_code;
		$info['response'] = $response;

		// Ensure to still display relevant errors on client's end.
		// Expect some proxy server errors.
		if ($auth_token && $auth_token->can_view_errors() && $http_code != 200) {
			preg_match("|(.*)\r\n|", $response, $match);
			$e =  new RequestException(@$match[1], $http_code);
			$e->setHeaders($headers);
			throw $e;
		}
		
		if (empty($error) && !empty($response)) {
			return $response;
		} else {
			return false;
		}
	}

	private function process_params($params) {
		if (empty($params['url'])) throw new RequestException("A URL is required");

		$this->url = $params['url'];
		$this->other_identifier = isset($params['other_identifier']) ? $params['other_identifier'] : null;

		if (!empty($params['retry_options'])) {
			$this->retry_options = $params['retry_options'];
		}

		if (!empty($params['proxy_request_options'])) {
			$this->proxy_request_options = $params['proxy_request_options'];
		}
	}

	
	private function is_post() {
		return !empty($this->params['post_data']);
	}
	
	private function should_redirect($http_code) {
		return in_array($http_code, array(301, 302, 307));
	}

	private function is_400($http_code) {
		return floor($http_code / 100) == 4;
	}

	/**
	 * Check if http code is 2xx
	 * 
	 * @param  string  $http_code
	 * @return boolean
	 */
	private function is_200($http_code) {
		return floor($http_code / 100) == 2;
	}

	/**
	 * Check if http code is exactly 403
	 * 
	 * @param string  $http_code
	 * @return boolean
	 */
	private function is_403($http_code) {
		return ($http_code == 403) ? true : false;
	}

	private function is_500($http_code) {
		return floor($http_code / 100) == 5;
	}

	private function get_current_proxy() {
		if (!isset($this->current_proxy))
			throw new Exception("Current proxy not set.");

		return $this->current_proxy;
	}

	private function track_proxy_usage($http_code, $module_detected_error) {
		$proxy = $this->get_current_proxy();
		$other_identifier = $this->other_identifier ? $this->other_identifier : '';
		ProxyServer::track_usage($proxy['id'], $this->module->identify(), $http_code, $module_detected_error, $other_identifier);
	}

	//Detect whether the current proxy was blocked, if so blacklist it and
	//return true.
	private function handle_proxy_failure($http_code) {
		if ($http_code == 407) {

			//use 407 if $response === false
			$this->track_proxy_usage(407, null);

			/**
			 * @todo Why has the request/proxy failed? Log this error somehow.
			 * Don't use an exception, as it'll interrupt what might otherwise
			 * be a successful subsequent request.
			 */
			$proxy = $this->get_current_proxy();
			$this->module->is_proxy_blacklisted($proxy, true);
			return true;
		}
		return false;
	}

	
	private function handleDataUnavailable($http_code, $headers, $response) {
		$auth_token = false;

		// Catch exception so it won't get error when running unit tests
		try {
			$auth_token = AuthToken::find_from_request();
		} catch (Exception $e) {
			$auth_token = false;
		}
		
		$msg = "Data Unavailable. Try again later.";
		if($auth_token && $auth_token->can_view_errors()) {
			// Ensure to display relevant errors on client's end.
			// Expect an error from thirdparty api server.
			if($_msg = $this->module->getErrorFromResponse($response)) {
				$msg = "Error from external api server: {$_msg}";
			}
		}
			
		$e =  new RequestException($msg, $http_code);
		$e->setHeaders($headers);
		throw $e;
	}

}
