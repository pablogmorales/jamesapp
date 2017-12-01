<?php

namespace Daytalytics\Module;

abstract class AlexaApi extends BaseModule {

    /**
     * We don't need to hide from Amazon - so don't use a proxy to get the data
     */
    protected $useProxies = false;
    
	protected $access_key = '';
	protected $secret_access_key = '';


	protected $ServiceHost	= 'awis.amazonaws.com';
	protected $SigVersion	= '2';
	protected $HashAlgorithm	= 'HmacSHA256';
	
	
	abstract protected function buildQueryParams();
	
	/**
	 * Builds current ISO8601 timestamp.
	 */
	protected function getTimestamp() {
		return gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time());
	}
	
	
	/**
	 * Generates an HMAC signature per RFC 2104.
	 *
	 * @param String $url	   URL to use in createing signature
	 */
	protected function generateSignature($url) {
		$sign = "GET\n" . strtolower($this->ServiceHost) . "\n/\n". $url;
		$sig = base64_encode(hash_hmac('sha256', $sign, $this->secret_access_key, true));
	
		return rawurlencode($sig);
	}
	
	protected function generate_rest_url() {
		$queryParams = $this->buildQueryParams();
	
		$signature = $this->generateSignature($queryParams);
		$url = 'http://' . $this->ServiceHost . '/?' . $queryParams . '&Signature=' . $signature;
		return  $url;
	}
	


}
