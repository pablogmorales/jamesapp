<?php

namespace Daytalytics\Module;

use Daytalytics\EndpointFailuresHandler;
use Exception;

abstract class EbayApi extends BaseModule {

    /*  We don't need to hide from eBay - so don't use a proxy to get the data */
    protected $useProxies = false;
    
	//production keys
	protected $keys_sets = array(
		'sandbox' => array(
			'devID' => '',
			'appID' => '',
			'certID' => '',
			'runame' => ''
		),
		'production' => array(
			'devID' => '',
			'appID' => '',
			'certID' => '',
			'runame' => ''
		),
	);

	protected $keys;
	/**/

	protected $endpoint;

	protected $site_codes = array(
		'us' => 0,
		'uk' => 3,
		'au' => 15,
		'ca' => 2,
	);

	protected $global_ids = array(
		'us' => 'EBAY-US',
		'uk' => 'EBAY-GB',
		'au' => 'EBAY-AU',
		'ca' => 'EBAY-ENCA',
	);

	function use_keys_set($set) {
		$this->keys = $this->keys_sets[$set];
	}

	function post_xml_request($xml_request, $http_headers, $curl_opts = array()) {
		if (!isset($this->endpoint)) {
			throw new Exception('Expected an endpoint to connect to.');
		}

		if (empty($keys)) {
			$this->keys = $this->keys_sets['production'];
		}

		$default_headers = array(
			'X-EBAY-API-VERSION: 647',
			'X-EBAY-API-DEV-NAME: ' . $this->keys['devID'],
			'X-EBAY-API-APP-NAME: ' . $this->keys['appID'],
			'X-EBAY-API-CERT-NAME: ' . $this->keys['certID'],
			'X-EBAY-API-COMPATIBILITY-LEVEL: 647',
			"X-EBAY-API-REQUEST-ENCODING: XML",
			'Content-Type: text/xml;charset=utf-8',
		);
		$headers = array_merge($default_headers, $http_headers);
		$curl_opts += array(CURLOPT_HTTPHEADER => $headers);
		$xml_response = $this->get($this->endpoint, $xml_request, $info, $curl_opts);

		return $xml_response;
	}

	function get_site_code($loc) {
		if (!isset($this->site_codes[$loc])) {
			throw new Exception('Unsupported ebay site location. '.$loc);
		}

		return $this->site_codes[$loc];
	}

	function get_global_id($loc) {
		if (!isset($this->global_ids[$loc])) {
			throw new Exception('Unsupported ebay global id location. '.$loc);
		}
		return $this->global_ids[$loc];
	}

	function ebay_time_to_timestamp($time) {
		$time = str_replace("T", " ", $time);
		$time = str_replace("Z", "", $time);
		$timestamp = strtotime($time);

		return $timestamp;
	}

	function ebay_duration_to_seconds($duration) {
		if(empty($duration)) {
			return 0;
		}
		
		//assume first character is P (as ebay says it should always be)
		$duration = substr($duration, 1);

		if(!$duration) {
			return;
		}
		
		list($period, $time) = explode("T", $duration);

		$strtotime_string = "";

		foreach (array('period' => $period, 'time' => $time) as $type => $data_set) {

			preg_match_all('/(\d+)(\D)/', $data_set, $matches, PREG_SET_ORDER);
			foreach ($matches as $match) {

				// Use a strtotime string instead of just multiplying by respective
				// quantities because, for example, the number of seconds in 1 month
				// depends from which month we start.
				// 1 month from 21 Mar is 31 days (21 Apr). 1 month from 21 Apr is 30 days (21 May).
				$value = (int)$match[1];

				switch ($type . '-' . $match[2]) {
					case 'period-Y' :
						$strtotime_string .= ' +' . $value . ' years';
						break;

					case 'period-M' :
						$strtotime_string .= ' +' . $value . ' months';
						break;

					case 'period-D' :
						$strtotime_string .= ' +' . $value . ' days';
						break;

					case 'time-H' :
						$strtotime_string .= ' +' . $value . ' hours';
						break;

					case 'time-M' :
						$strtotime_string .= ' +' . $value . ' minutes';
						break;

					case 'time-S' :
						$strtotime_string .= ' +' . $value . ' seconds';
						break;

					default :
						throw new Exception('Unexpected Ebay Duration.' . $duration);
				}
			}
		}
		$time_now = time();
		$time_after_duration = strtotime($strtotime_string, $time_now);

		$seconds = $time_after_duration - $time_now;
		return $seconds;
	}

	/**
	 * Records all failed requests to API, so that we do not abuse it.
	 * @param $headers List of headers from curl
	 */
	protected function handleEbayException($headers) {
		// Check is 403
		if ($headers['http_code'] == 403) {
			EndpointFailuresHandler::save($this->name);
		}
	}
}
