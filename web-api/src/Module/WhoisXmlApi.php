<?php

namespace Daytalytics\Module;

use Daytalytics\DataGetter;
use Daytalytics\RequestException;
use SimpleXMLElement;

class WhoisXmlApi extends BaseModule {

	protected $auth = array(
		'username' => '', 
		'password' => ''
	);
	
	/**
	 * xml or json
	 */
	protected $outputFormat = 'xml';
	
	protected $raw = false;
	
	public function define_service() {
	    return [
	        'default' => [
	            'parameters' => [
	                'domain' => [
	                    'description' => 'The domain to get whois data for, e.g. example.com',
	                    'required' => true
	                ],
	                'outputFormat' => [
	                    'description' => 'Return whois results in xml, json or raw format',
	                    'options' => ['xml', 'json', 'raw'],
	                    'default' => 'raw'
	                ]
	            ]
	        ]
	    ];
	}

	public function handle_request(array $params = []) {
		if(!isset($params['domain'])) {
			throw new RequestException('A domain must be specified.');
		}

		if (!isset($params['outputFormat']) || $params['outputFormat'] == 'raw') {
		    $this->raw = true;
		} else {
		    if(!in_array($params['outputFormat'], array('json', 'xml'))) {
		        throw new RequestException('A valid output format must be specified.');
		    }
		    $this->outputFormat = $params['outputFormat'];
		}
		
		$data = $this->get_data($params['domain']);
		
		if($this->raw) {
			return $this->extract_raw_text_from_data($data);
		} else {
			return $data;
		}
	}

	function get_data($domain){
		$dg_params = array(
			'cache_key' => serialize(array("whois" => array($domain, $this->outputFormat, $this->raw))),					
			'data_sources' => $this->format_data_sources_for_data_getter(array("local", "live")),
			'url_getter_params' => array(
				'url' => $this->request_url($domain)
			),
		);
		return DataGetter::get_data_or_throw($this, $dg_params);
	}

	function request_url($domain) {
		return sprintf("https://www.whoisxmlapi.com/whoisserver/WhoisService?domainName=%s&username=%s&password=%s&outputFormat=%s", 
			$domain, $this->auth['username'], $this->auth['password'], $this->outputFormat);
	}

	function extract_raw_text_from_data($data) {
		$output = '';
		if($this->outputFormat == 'xml') {
			$x = new SimpleXMLElement($data);
			if ($x->rawText->asXML() !== false) {
				$output = (string)$x->rawText;
			} else {
				$output = (string)$x->registryData->rawText;
			}
		} else if($this->outputFormat == 'json') {
			$json = json_decode($data);
			$output = $json->WhoisRecord->registryData->rawText;
		}
		
		return $output;
	}
}
