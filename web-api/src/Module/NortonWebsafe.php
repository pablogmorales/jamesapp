<?php

namespace Daytalytics\Module;

use Daytalytics\DataGetter;
use Daytalytics\RequestException;
use DOMDocument;
use DOMXPath;
/**
 * Norton Websafe server module w/c parses the content string from request url eg. https://safeweb.norton.com/report/show?url=salehoo.com
 * It looks for these below elements to determine safety rating.
 * 
 * <div class="big_rating_wrapper">
 * 	<img src="" alt="icoNSecured|icoSafe|icoCaution|icoWarning|icoUntested" class="big_clip icoNSecured|icoSafe|icoCaution|icoWarning|icoUntested">
 * </div>
 */
class NortonWebsafe extends BaseModule {
	
	protected $endpoint = 'http://safeweb.norton.com/report/show';
	
	protected $iconToRating = array(
		'icoNSecured' => 'Norton Secured',
		'icoSafe' => 'Safe',
		'icoCaution' => 'Caution',
		'icoWarning' => 'Warning',
		'icoUntested' => 'Untested'
	);
	
	/**
	 * These are the icon string w/c are considered as safe
	 * 
	 * @var array
	 */
	protected $safeIcons = array(
		'icoNSecured',
		'icoSafe'
	);
	
	
	public function define_service() {
	    return [
	        'safetyratingbydomain' => [
	            'parameters' => [
	                'domain' => [
	                    'description' => 'Domain to check e.g. example.com',
	                    'required' => true
	                ]
	            ]
	        ]
	    ];
	}
	
	/**
	 * 
	 * @param unknown $params
	 * @throws RequestException
	 * @return multitype:
	 */
	public function handle_request(array $params = []) {		
		switch (@$params['type']) {
			case 'safetyratingbydomain':
				$result = $this->searchSafetyRatingByDomain(@$params['domain']);
			break;
			default:
				throw new RequestException('A valid type is required');
			break;
		}
		return $result;
	}
	
	
	public function searchSafetyRatingByDomain($domain) {
		if(empty($domain)) {
			throw new RequestException('Domain is required');
		}
		
		$requestUrl = "{$this->endpoint}?url={$domain}";
		$cacheKey = sha1($requestUrl);
		$cachedData = $this->db->get_parsed_data($cacheKey, '-1 day', $this->identify());
		if($cachedData && $result = unserialize($cachedData)) {
			return $result;
		}
		
		$htmlOutput = $this->request($requestUrl);
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhitespace = true;
		@$dom->loadHTML($htmlOutput);
		$xpath = new DOMXPath($dom);
		
		$imgElement = $xpath->query('//div[@class="big_rating_wrapper"]//img');
		$result = array(
			'is_safe' => 0,
			'rating' => 'Unknown'
		);

		if($imgElement->length > 0) {
			$safeIcon = $imgElement->item(0)->getAttribute('alt');
			$result['is_safe'] = (int) $this->isSafe($safeIcon);
			$result['rating'] = $this->getSafeRatingByIcon($safeIcon);
			
			$this->db->set_parsed_data($cacheKey, serialize($result), $this->identify());
			return $result;
		}
		
		// They might have changed the html structure :(
		throw new RequestException('Data not available.');
	}
	

	public function isSafe($icon) {
		return in_array($icon, $this->safeIcons);
	}
	
	public function getSafeRatingByIcon($icon) {
		if(!empty($this->iconToRating[$icon])) {
			return $this->iconToRating[$icon];
		}
		return 'Unknown';
	}
	
	protected function request($requestUrl) {
		$cacheKey = sha1($requestUrl);
		$dataGetterParams = array(
			'cache_key' => $cacheKey,
			'data_sources' => $this->format_data_sources_for_data_getter(),
			'url_getter_params' => array(
				'url' => $requestUrl,
				'user_agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:44.0) Gecko/20100101 Firefox/44.0', // newer browser agent
				'curl_options' => array(
					CURLOPT_FOLLOWLOCATION => true
				)
			)
		);
		
		return DataGetter::get_data_or_throw($this, $dataGetterParams);
	}
	
	
}