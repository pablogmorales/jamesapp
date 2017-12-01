<?php

namespace Daytalytics;

class BasicRequest implements RequestInterface {
	
	protected $user_agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.11) Gecko/2009060215 Firefox/3.0.11';
	
	protected $timeout = 60;

	protected $post_data = [];
	
	protected $curl_options = [];
	
	function __construct(array $params = []) {
		$this->process_params($params);
	}

	protected function process_params($params) {
		if (!empty($params['timeout']) && is_numeric($params['timeout'])) {
			$this->timeout = $params['timeout'];
		}
		
		if (!empty($params['post_data'])) {
			$this->post_data = $params['post_data'];
		}

		if (!empty($params['curl_options'])) {
			$this->curl_options = $params['curl_options'];
		}

        if (!empty($params['user_agent'])) {
			$this->user_agent = $params['user_agent'];
        }
	}	

	public function getResponse($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_ENCODING, '');

		// It doesn't hurt to always set these, and it means we can change the url
		// to https:// below
		//!@ToDo: Set this to 2 when the server has a valid secure certificate
		// Set to 2 to fix error:
		// Notice: curl_setopt(): CURLOPT_SSL_VERIFYHOST with value 1 is deprecated and will be removed as of libcurl 7.28.1. It is recommended to use value 2
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		if ($this->is_post()) {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->post_data);
		}
		
		if (!empty($this->curl_options)) {
			curl_setopt_array($ch, $this->curl_options);
		}
		
		
		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$error_number = curl_errno($ch);
		$headers = $this->extract_headers($response, $ch);
		$error = curl_error($ch);
		$headers_out = curl_getinfo($ch, CURLINFO_HEADER_OUT);
		curl_close($ch);
			
		return compact('response', 'http_code', 'headers', 'headers_out', 'error', 'error_number');
	}
	
	protected function is_post() {
		return !empty($this->post_data);
	}

	//extract and remove the headers from the response, and merge them with the
	//curl headers
	protected function extract_headers(&$response, $curl_handle) {
		$headers = array();
	
		if($response !== false) {
	
			$pos = strpos($response, "\r\n\r\n");
			if ($pos !== false) {
				$header_string = substr($response, 0, $pos);
				preg_match_all("/([^\r\n:]+):[ ]+([^\r\n]*)/", $header_string, $matches, PREG_SET_ORDER);
				foreach($matches as $match) {
					$headers[$match[1]] = $match[2];
				}
				$body_pos = $pos + strlen("\r\n\r\n");
				if ($body_pos < strlen($response) - 1) {
					$response = substr($response, $body_pos);
				}
				else {
					$response = "";
				}
			}
		}
	
		$headers = array_merge($headers, curl_getinfo($curl_handle));
	
		return $headers;
	}
}
