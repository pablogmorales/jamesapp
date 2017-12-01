<?php

namespace Daytalytics\Module;

use Daytalytics\RequestException;
use Daytalytics\DataGetter;
use SimpleXMLElement;

class Alexa extends AlexaApi {

	public $domain;
	
	protected $ActionName = 'UrlInfo';

	protected $ResponseGroupName = 'Rank';
	
	private $data_type = 'rank';
	
	private $startfrom = 0;
	
	public function define_service() {
	    $params = [
	        'url'=> [
	            'description' => 'Website url',
	            'required' => true
	        ],
	        'startfrom' => [
	            'description' => 'Return backlinks from this number',
	            'type' => 'number',
	            'default' => 0
	        ]
	    ];
		return [
		    'rank' => [
                'parameters' => [
                    'url' => $params['url']
                ]
            ],
            'backlinks' => [
                'parameters' => $params              
            ]
		];
	}

	public function handle_request(array $params = []) {
		if(!isset($params['url']) || empty($params['url'])) {
			throw new RequestException('A url is required.');
		}
		$this->set_domain($params["url"]);
		$params['type'] = @$params['type']?: 'rank';
		$this->data_type = $params['type'];
		switch ($this->data_type) {
			case 'rank':
			    $this->ActionName = 'UrlInfo';
			    $this->ResponseGroupName = 'Rank';
				return $this->get_alexa_rank();
				break;
			case 'backlinks':
				if(isset($params['startfrom'])) {
					$this->startfrom = $params["startfrom"];
				}
				$this->ActionName = 'SitesLinkingIn';
				$this->ResponseGroupName = 'SitesLinkingIn';
				return $this->get_alexa_site_linking_in();
				break;
			default:
				throw new RequestException("Invalid request type");
				break;
		}		
	}


	public function set_domain($domain) {
		if (is_array($domain)) {
			$domain = current(reset($domain));
		}
		if (is_string($domain)) {
			preg_match('#^(http://)?([^?/]*)([?/].*)?$#', $domain, $matches);
			$this->domain = $matches[2];
		}
	}

	public function get_alexa_rank() {
		$cache_key = "$this->domain:$this->data_type";
		$request = $this->generate_rest_url();

		$dg_params = array(
			'cache_key' => $cache_key,
			'cache_expiry_time' => '-24 hours',
			'data_sources' => $this->format_data_sources_for_data_getter(),
			'url_getter_params' => array(
				'url' => $request
			)
		);
		$response = DataGetter::get_data_or_throw($this, $dg_params);

		if (preg_match('#<aws:Rank/>#', $response)) {
			throw new RequestException('This page is not ranked.');
		}

		preg_match('#<aws:Rank>([\d]+)</aws:Rank>#s', $response, $m);
		$rank = $m[1];
		return (int)$rank;
	}

	public function get_alexa_site_linking_in() {
		$cache_key = "$this->domain:$this->data_type:$this->startfrom";
		$request = $this->generate_rest_url();

		$dg_params = array(
			'cache_key' => $cache_key,
			'cache_expiry_time' => '-24 hours',
			'data_sources' => $this->format_data_sources_for_data_getter(),
			'url_getter_params' => array(
				'url' => $request
			)
		);
		
		$response = DataGetter::get_data_or_throw($this, $dg_params);

		if (preg_match('#<aws:SitesLinkingIn/>#', $response)) {
			throw new RequestException('This page is not linked.');
		}
		
		return $this->parse_backlink_list_data($response);;
	}

	public function should_retry_request($response, $retry_options = []) {
		switch ($this->data_type) {
			case 'rank':
				preg_match('#(<aws:Rank>[\d]+</aws:Rank>|<aws:Rank/>)#s', $response, $m);
				break;
			case 'backlinks':
				preg_match('#(<aws:SitesLinkingInResult>)#s', $response, $m);
				break;
			default:
				throw new RequestException("Invalid parameter data_type");
				break;
		}
		
		if (!isset($m[0])) {

			$expected_alexa_errors = array(
				'TimeoutError',
				'AlexaError',
				'ServiceUnavailable' => '<body><b>Http/1.1 Service Unavailable</b></body>',
			);

			foreach ($expected_alexa_errors as $key => $expected_alexa_error) {
				if (is_numeric($key)) {
					$error_token = '<aws:ResponseStatus><aws:StatusCode>' . $expected_alexa_error . '</aws:StatusCode>';
				}
				else {
					$error_token = $expected_alexa_error;
				}
				if (strpos($response, $error_token) !== false) {
					return true;
				}
			}

			throw new RequestException('Unexpected Data. ' . $response);
		}

		return false;
	}

	
  
	/**
	 * Builds query parameters for the request to AWIS.
	 * Parameter names will be in alphabetical order and
	 * parameter values will be urlencoded per RFC 3986.
	 * @return String query parameters for the request
	 */
	protected function buildQueryParams() {
		$params = array(
			'Action'			=> $this->ActionName,
			'ResponseGroup'		=> $this->ResponseGroupName,
			'AWSAccessKeyId'	=> $this->access_key,
			'Timestamp'			=> $this->getTimestamp(),		   
			'SignatureVersion'	=> $this->SigVersion,
			'SignatureMethod'	=> $this->HashAlgorithm,
			'Url'				=> $this->domain
		);
		if ($this->data_type =='backlinks'){
			$params['Count'] = 20;
			$params['Start'] = $this->startfrom;
		}
		
		ksort($params);
		$keyvalue = array();
		foreach($params as $k => $v) {
			$keyvalue[] = $k . '=' . rawurlencode($v);
		}
		return implode('&',$keyvalue);
	}
	
	function parse_backlink_list_data($data) {
		preg_match('#<aws:SitesLinkingIn>(.*?)</aws:SitesLinkingIn>#s', $data, $m);
		$modifiedxml = $m[0];
		$modifiedxml = str_replace('<aws:', '<', $modifiedxml);
		$modifiedxml = str_replace('</aws:', '</', $modifiedxml);
		$xml = new SimpleXMLElement($modifiedxml);
		
		for ($i = 0; $i < $xml->Site->count(); $i++){
			$data_keyvalues[$i]['title'] = (String)$xml->Site[$i]->Title;
			$data_keyvalues[$i]['url'] = (String)$xml->Site[$i]->Url;
		};

		$data_attributes = array(
			'RowsCount'=> (String)$xml->Site->count(),
		);
		
		return array('info'=>$data_attributes, 'backlink'=>$data_keyvalues);
	}
}
?>
