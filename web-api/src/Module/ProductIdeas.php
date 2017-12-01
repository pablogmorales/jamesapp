<?php

namespace Daytalytics\Module;

use Daytalytics\RequestException;
use Daytalytics\DataGetter;
use Daytalytics\Xml;

class ProductIdeas extends BaseModule {
	
	protected $useProxies = false;
	
	
	public function define_service() {
		return [
		    'categories' => [
		        'parameters' => [
		            'feed' => [
		            	'required' => true,
		            	'options' => [
		            		'wishedlist',
		            		'amazon', // alias of wishedlist 
		            		'ali'
		            	]		
		            ]
		        ]
		    ]
		];
	}
	
	
	protected function getFeedUrl($feed) {
		switch($feed) {
			case 'wishedlist':
			case 'amazon':
				return '';
				break;
			case 'ali':
				return '';
				break;
		}
		
		return false;
	}
	
	
	/**
	 * 
	 * @param unknown $params
	 * @throws RequestException
	 * @return multitype:
	 */
	public function handle_request(array $params = []) {
		if (empty($params['feed'])) {
			throw new RequestException("Param 'feed' is required.");
		}
		
		if(!$this->getFeedUrl($params['feed'])) {
			throw new RequestException('A valid feed is required');
		}
		
		switch (@$params['type']) {
			case 'categories':
			    $requestParams = array_intersect_key($params, ['feed' => '']);
				$result = $this->getCategories($requestParams);
			break;
			default:
				throw new RequestException('A valid type is required');
			break;
		}
		return $result;
	}
	
	
	public function getCategories(array $params = []) {
		$requestUrl = $this->getFeedUrl($params['feed']);
		$cacheKey = sha1($requestUrl);
		
		$dataGetterParams = array(
			'cache_key' => $cacheKey,
			'data_sources' => $this->format_data_sources_for_data_getter(),
			'url_getter_params' => array(
				'url' => $requestUrl,
				'curl_options' => array (
					CURLOPT_TIMEOUT => 0,
				)
			)
		);
		
		$xmlData = DataGetter::get_data_or_throw($this, $dataGetterParams);
		
		if($xmlData) {
			$xml = simplexml_load_string($xmlData);
			return Xml::toArray($xml);
		}
		throw new RequestException('Data not available.');
	}
}