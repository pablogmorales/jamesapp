<?php

namespace Ddm\Deploy\Notification;

trait CurlHttpTrait {

	protected $headers = [];

	protected $data = [];

	protected $url = '';

	protected $method = 'POST';

	protected $curlOpts = [];

	public $result;

	public $error;

	public $code;

	protected function send() {
		$ch = curl_init();
		unset($this->result, $this->error, $this->code);
		$methods = [
			'get' => CURLOPT_HTTPGET,
			'post' => CURLOPT_POST,
			'put' => [CURLOPT_CUSTOMREQUEST, 'PUT'],
			'head' => CURLOPT_NOBODY,
			'delete' => [CURLOPT_CUSTOMREQUEST, 'DELETE']
		];
		$requestUrl = $this->url;
		$method = strtolower($this->method);
		$curlRequestMethod = array_key_exists($method, $methods) ? $methods[$method] : CURLOPT_HTTPGET;
		$requestMethodValue = true;
		if (is_array($curlRequestMethod)) {
			list($curlRequestMethod, $requestMethodValue) = $curlRequestMethod;
		}
		curl_setopt($ch, $curlRequestMethod, $requestMethodValue);
		if(!empty($this->data)){
			$data = is_array($this->data) ? http_build_query($this->data) : $this->data;
			if ($method == 'post' || $method == 'put') {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			} else {
				$concat = strpos($requestUrl, '?') ? '&' : '?';
				$requestUrl.= $concat . $data;
			}
		}

		curl_setopt($ch, CURLOPT_URL, $requestUrl);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

		if(!empty($this->headers)){
			$headers = [];
			if (is_string($this->headers)) {
				$headers[] = $this->headers;
			} else {
				foreach ($this->headers as $name => $header) {
					if (!is_int($name)) {
						$header = "{$name}:{$header}";
					}
					$headers[] = $header;
				}
			}
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}

		if(!empty($this->curlOpts)){
			curl_setopt_array($ch, $this->curlOpts);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$this->result = curl_exec($ch);
		$this->error = curl_error($ch);
		$this->code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
	}

	public function __call($name, array $arguments) {
		if (($set = strpos($name, 'set') === 0) || ($get = strpos($name, 'get') === 0)) {
			$property = strtolower(substr($name, 3));
			if (property_exists($this, $property)) {
				if ($set && array_key_exists(0, $arguments)) {
					$this->{$property} = $arguments[0];
				}
				return $this->{$property};
			}
		}
	}

}