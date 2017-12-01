<?php

namespace Daytalytics\Module;

use Daytalytics\DataGetter;
use Daytalytics\RequestException;
use Exception;
use DOMDocument;
use DOMXPath;

class TradeShow extends BaseModule {

	protected $cacheExpiry = '-7 days';
	
	protected $baseUrl = 'http://10times.com/';
	
	protected $countriesUrl = 'tradeshows/by-country';
	
	protected $ajaxUrl = 'ajax?for=event_listing&ajax=1&country=%s&event_type=1';
	
	protected $countryAndCityAjaxUrlFormat = '%s-%s/tradeshows?ajax=1&page=%s'; // city-country
	
	/**
	 * Increment this value whenever needed an immediate and refresh results
	 */
	const CACHE_BUSTER = 2;
	
	/**
	 * Preferred countries are crawled first
	*/
	protected $_preferentialCountries = array(
		'USA',
		'UK',
		'Canada',
		'Australia',
		'New Zealand',
		'Singapore',
		'China',
		'Norway'
	);

	public function define_service() {
	    return [
	        'countries' => [
	            'parameters' => []
	        ],
	        'eventssummarybycountry' => [
	            'parameters' => [
	                'countryCode' => [
	                    'description' => 'Country',
	                    'required' => true
	                ]
	            ]
	        ],
	        'eventlistbycountryandcity' => [
	            'parameters' => [
	                'countryCode' => [
	                    'description' => 'Country code',
	                    'required' => true
	                ],
	                'city' => [
	                    'description' => 'City name',
	                    'required' => true
	                ]
	            ]
	        ]
	    ];
	}

	/**
	 *
	 * @param array $params
	 * @throws RequestException
	 * @return array
	 */
	public function handle_request(array $params = []) {
		if(!isset($params['type'])) {
			throw new RequestException('Type is required.');
		}
		switch ($params['type']) {
			case 'countries':
				$result = $this->getCountries($params);
				break;
				
			case 'eventssummarybycountry':
				$result = $this->getEventsSummaryByCountry($params);
				break;
				
			case 'eventlistbycountryandcity':
				$result = $this->getEventListByCountryAndCity($params);
				break;
			
			default:
				throw new RequestException('A valid type is required');
				break;
		}
		return $result;
	}
	
	protected function fetchData($requestUrl) {
		$cacheKey = sha1($requestUrl) . static::CACHE_BUSTER;
		$dataGetterParams = array(
			'cache_key' => $cacheKey,
			'cache_expiry_time' => $this->cacheExpiry,	
			'data_sources' => $this->format_data_sources_for_data_getter(),
			'url_getter_params' => array(
				'url' => $requestUrl
			)
		);
		
		return DataGetter::get_data_or_throw($this, $dataGetterParams);
	}
	
	public function getCountries() {
		$requestUrl = $this->baseUrl . $this->countriesUrl;
		$cacheKey = sha1($requestUrl);
		$countries = array();
		$countriesCacheData = $this->db->get_parsed_data($cacheKey, $this->cacheExpiry, $this->identify());
		
		if($countriesCacheData && $countries = unserialize($countriesCacheData)) {
			return $countries;
		} 
		
		$html = $this->fetchData($requestUrl);
		if(empty($html)) {
			return array();
		}
			
		preg_match('#<select[^>]*?name="country" id="country"[^>].*?</select>#si', $html, $countrySelectHtmlMatch);
		preg_match_all('#<option.*?value="/?([^"]+)">([^<]+)</option>#si', $countrySelectHtmlMatch[0], $countriesMatch, PREG_SET_ORDER);
			
		$countryList = $preferredList = array();
		foreach ($countriesMatch as $match) {
			$countryList[$match[1]] = $match[2];
		}
		//apply preferential countries in list order
		foreach ($this->_preferentialCountries as $preferred) {
			if ($index = array_search($preferred, $countryList)) {
				$preferredList[$index] = $countryList[$index];
				unset($countryList[$index]);
			}
		}
			
		$countries = $preferredList + $countryList;
		$this->db->set_parsed_data($cacheKey, serialize($countries), $this->identify());
				
		return $countries;
	}

	public function getEventsSummaryByCountry($params) {
		if(empty($params['countryCode'])) {
			throw new RequestException('Country code is required');
		}
		
		$events = array();
		$requestUrl = $this->baseUrl . sprintf($this->ajaxUrl, $params['countryCode']);
		
		$rawData = $this->fetchData($requestUrl);
		if ($rawData) {
			$events = json_decode($rawData, true);
		}
		return $events;
	}
		
	public function getEventListByCountryAndCity($params) {
		if(empty($params['countryCode']) || empty($params['city'])) {
			throw new RequestException('Country code and city are required');
		}
		
		$cacheKey = sha1($this->baseUrl . serialize($params));
		$eventsCacheData = $this->db->get_parsed_data($cacheKey, $this->cacheExpiry, $this->identify());
		if($eventsCacheData && $events = unserialize($eventsCacheData)) {
			return $events;
		}
		
		$events = array();
		$page = 1;
		$isValidPage = true;
		
		while($isValidPage) {
			$requestUrl = $this->baseUrl . sprintf($this->countryAndCityAjaxUrlFormat, $params['city'], strtolower($params['countryCode']), $page);
			
			try {
				$html = $this->fetchData($requestUrl);
				$dom = new DOMDocument('1.0', 'UTF-8');
				$dom->preserveWhitespace = true;
				@$dom->loadHTML($html);
					
				$xpath = new DOMXPath($dom);
				$eventElements = $xpath->query("//*[@itemtype='//schema.org/Event']");
					
				if(!empty($eventElements)) {
			
					foreach ($eventElements as $elem) {
						$event = $industries = array();
			
						$eventHtml = $dom->saveHTML($elem);	
						$eventDom = new DOMDocument('1.0', 'UTF-8');
						$eventDom->loadHTML($eventHtml);
			
						$xpath = new DOMXPath($eventDom);
						$name = $xpath->query("//*[@itemprop='name']")->item(0);
						$url = $xpath->query("//*[@itemprop='url']")->item(0);
						$startDate = $xpath->query("//*[@itemprop='startDate']")->item(0);
						$endDate = $xpath->query("//*[@itemprop='endDate']")->item(0);
						$eventUrl = "http://10times.com{$url->getAttribute('href')}";
						
						$event['title'] = $name->nodeValue;
						$event['guid'] = $eventUrl;
						$event['date_start'] = $startDate->getAttribute('content');
						$event['date_end'] = $endDate->getAttribute('content');
						$event['industries'] = $this->getIndustries($xpath);
						$event['organizer'] = $this->getOrganizer($eventUrl);
						
						$event['_page'] = $page;
						$events[] = $event;
					}
				}

				$page++;
				
			} catch (Exception $e) {
				// If page 1 and it fails, provided country code or city might be invalid				
				if($page == 1) {
					throw new RequestException('Invalid country code or city parameters.');
				}
				
				$isValidPage = false;
			}
		}
		
		$this->db->set_parsed_data($cacheKey, serialize($events), $this->identify());
		return $events;
	}
	
	protected function getIndustries($xpath) {
		$_industries = $xpath->query('//*[contains(@class,"label-tag")]');
		$industries = array();
		foreach ($_industries as $industry) {
			$industries[] = trim($industry->nodeValue);
		}
		return $industries;
	}
	
	protected function getOrganizer($url) {
		$html = $this->fetchData($url);
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhitespace = true;
		@$dom->loadHTML($html);
		$xpath = new DOMXPath($dom);
		return @$xpath->query("//*[@itemtype='//schema.org/Organization']//*[@itemprop='name']")->item(0)->nodeValue 
			? : '';
		
	}
	
	
}
