<?php

namespace Daytalytics\Module;

use Daytalytics\RequestException;
use Daytalytics\DataGetter;
use SimpleXMLElement;

class AlexaTopSites extends AlexaApi {

	public $name = 'AlexaTopSites';
	public $version = 1.0;
	
	
	protected $ServiceHost	= 'ats.amazonaws.com';
	protected $ActionName        = 'TopSites';
	protected $ResponseGroupName = 'Country';
	protected $NumReturn         = 100;
	protected $StartNum          = 1;
	
	protected $countryCode;
	

	public function define_service() {
		return [
		    'default' => [
		        'parameters' => [
		            'countryCode' => [
		                'required' => true
		            ],
		            'numReturn' => [
		                'default' => 100
		            ],
		            'startNum' => [
		                'default' => 1
		            ]
		        ]
		    ]  
		];
	}

	public function handle_request(array $params = []) {
		
		if(empty($params['countryCode'])) {
			throw new RequestException("Country code parameter is required.");
		}
		
		$this->countryCode = $params['countryCode'];
		
		if(!empty($params['numReturn'])) {
			$this->NumReturn = (int) $params['numReturn'];
		}
		
		if(!empty($params['startNum'])) {
			$this->StartNum = (int) $params['startNum'];
		}
		
		return $this->getTopSites();
	}
	
	
	protected function buildQueryParams() {
		$params = array(
			'Action'            => $this->ActionName,
			'ResponseGroup'     => $this->ResponseGroupName,
			'AWSAccessKeyId'    => $this->access_key,
			'Timestamp'         => $this->getTimestamp(),
			'CountryCode'       => $this->countryCode,
			'Count'             => $this->NumReturn,
			'Start'             => $this->StartNum,
			'SignatureVersion'  => $this->SigVersion,
			'SignatureMethod'   => $this->HashAlgorithm
		);
		ksort($params);
		return http_build_query($params);
	}
	
	
	public function getTopSites() {
		$requestUrl = $this->generate_rest_url();
		$cacheKey = sha1($requestUrl);
		$topSitesCacheData = $this->db->get_parsed_data($cacheKey, '-1 day', $this->identify());
		
		if($topSitesCacheData && $topSites = unserialize($topSitesCacheData)) {
			return $topSites;
		}
		
		$dataGetterParams = array(
			'cache_key' => $cacheKey,
			'data_sources' => $this->format_data_sources_for_data_getter(),
			'url_getter_params' => array(
				'url' => $requestUrl
			)
		);

		$rawData = DataGetter::get_data_or_throw($this, $dataGetterParams);
		
		$topSites = array();
		$xml = new SimpleXMLElement($rawData, null, false, 'http://' . $this->ServiceHost . '/doc/2005-11-21');
		foreach($xml->Response->TopSitesResult->Alexa->TopSites->Country->Sites->children('http://' . $this->ServiceHost . '/doc/2005-11-21') as $site) {
			$topSites[] = (string) $site->DataUrl;
		}
		
		$this->db->set_parsed_data($cacheKey, serialize($topSites), $this->identify());
		return $topSites;
	}
	

}

