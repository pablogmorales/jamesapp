<?php

namespace Daytalytics;

class ProxyRequest extends BasicRequest {
	
	private $referer = 'https://www.google.com';
	private $encoding = '';
	private $cookie_file = '';
	private $proxy;
	
	public function __construct($proxy, array $params = []) {
		parent::__construct($params);
		$this->proxy = $proxy;
		$this->process_params($params);
	}

	
	public function getResponse($url) {
		
		$headers = [];
		$error = false;
		$http_code = 0;
		$headers_out = '';
		$response = false;
		
		$curl = curl_init();
		$url_parts = parse_url($url);
		if ($url_parts !== false) {
			$url = '';
			if(!isset($url_parts['scheme'])) {
				$url_parts['scheme'] = 'http';
			}
			$url .= $url_parts['scheme'] . '://';

			//try to correct the host
			if(!isset($url_parts['host']) && isset($url_parts['path'])) {
				$url_parts2 = parse_url('http://'.$url_parts['path']);

				if(isset($url_parts2['host']))
					$url_parts['host'] = $url_parts2['host'];
				if(isset($url_parts2['path']))
					$url_parts['path'] = $url_parts2['path'];
				else
					unset($url_parts['path']);
			}
			if(isset($url_parts['host'])) {
				$url .= $url_parts['host'];
	
				if(!isset($url_parts['path'])) {
					$url_parts['path'] = '/';
				}
				$url .= $url_parts['path'];
	
				if(!isset($url_parts['query'])) {
					$url_parts['query'] = '';
				}
				if($url_parts['query']) {
					$url .= '?' . $url_parts['query'];
				}
	
				if(!isset($url_parts['fragment'])) {
					$url_parts['fragment'] = '';
				}
				if($url_parts['fragment']) {
					$url .= ''; //'#' . $url_parts['fragment'];
				}

				$curl_options = array(
                    CURLOPT_URL => $url,
                    CURLOPT_USERAGENT => $this->user_agent,
                    CURLOPT_REFERER => $this->referer,
                    CURLOPT_ENCODING => $this->encoding,
                    CURLOPT_AUTOREFERER => true,
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_TIMEOUT => $this->timeout,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_ENCODING => '',
                    CURLOPT_HEADERFUNCTION => array(&$this,'readHeader'),
                    CURLOPT_FRESH_CONNECT => true,
                    CURLOPT_FORBID_REUSE => true
                );

                $proxy_string = "{$this->proxy['IP']}:{$this->proxy['port']}";
                $curl_options[CURLOPT_PROXY] = $proxy_string;
                $auth_string = "{$this->proxy['username']}:{$this->proxy['password']}";
                $curl_options[CURLOPT_PROXYUSERPWD] = $auth_string;
	
				if(isset($url_parts['port'])) {
					$curl_options[CURLOPT_PORT] = $url_parts['port'];
				}
	
				if(isset($url_parts['username'])) {
					$curl_options[CURLOPT_HTTPAUTH] = $url_parts['username'] . (isset($url_parts['password'])?':'.$url_parts['password']:'');
				}
				
				if($url_parts['scheme']=='https'){
					$curl_options[CURLOPT_SSL_VERIFYHOST] =  2;
					$curl_options[CURLOPT_SSL_VERIFYPEER] = false;
				}
				
				if(!empty($this->cookie_file)) {
					$curl_options[CURLOPT_COOKIEJAR] = $this->cookie_file;
					$curl_options[CURLOPT_COOKIEFILE] = $this->cookie_file;
				}
					
				if (!empty($this->post_data)) {
					$curl_options[CURLOPT_POST] = true;
					$curl_options[CURLOPT_POSTFIELDS] = $this->post_data;
				}

                if (!empty($this->curl_options)) {
                    $curl_options = $this->curl_options + $curl_options;
                }

                curl_setopt_array($curl, $curl_options);
				
				$response = curl_exec($curl);
				
				$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				$error_number = curl_errno($curl);
				$headers = $this->extract_headers($response, $curl);
				$error = curl_error($curl);
				$headers_out = curl_getinfo($curl, CURLINFO_HEADER_OUT);
				curl_close($curl);
			} else {
				$response = 'The request could not be understood by the server due to malformed syntax. The client SHOULD NOT repeat the request without modifications.';
				$error = true;
			}
		}
		
		return compact('response', 'http_code', 'headers', 'headers_out', 'error', 'error_number');
	}

	function readHeader($ch, $header) {
		$this->response_headers[] = trim($header);
		return strlen($header);
	}

	function setCookieFile($filename) {
		$filename = preg_replace('/[^A-z0-9]+/', '', $filename);
		$base = dirname(__FILE__).'/cookies/';
		if (file_exists($base)) {
			$this->cookie_file = $base . $filename;
			if (!file_exists($this->cookie_file)) {
				$this->clearCookieFile();
			}
		}
	}

	function clearCookieFile() {
		if ($this->cookie_file == '') return false;
		$fp = fopen($this->cookie_file, 'w');
		fwrite($fp, '');
		fclose($fp);
	}

}
