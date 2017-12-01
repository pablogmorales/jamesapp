<?php

namespace Daytalytics\Module;

use Daytalytics\DataGetter;
use Daytalytics\RequestException;
use DateTime;
use DateTimeZone;
use Exception;
use SimpleXMLElement;

class EbayFindingApi extends EbayApi {

	protected $endpoint = 'http://svcs.ebay.com/services/search/FindingService/v1';

	public function define_service() {
	    $types = [
	        'finditemsbykeywords',
	        'finditemscompletedbykeywords',
	        'finditemstotalbykeywords',
	        'finditemscompletedtotalbykeywords',
	        'findsolditemstotalbykeywords',
	        'finditemsadvancedtotalbykeywords',
	        'findcatgoriesbykeywords',
	        'findcurrency'
	    ];
	    $params = [
	        'keyword' => [
	            'description' => 'Keyword search term',
	            'required' => true
	        ],
	        'loc' => [
	            'description' => 'Location',
	            'required' => true,
	            'type' => 'number',
	            'options' => $this->global_ids
	        ],
	        'start' => [
	            'description' => 'Start time',
	            'format' => 'dateTime'
	        ],
	        'end' => [
	            'description' => 'End time',
	            'format' => 'dateTime'
	        ]
	    ];
	    return array_fill_keys($types, ['parameters' => $params]);
	}

	/**
	 *
	 * @param array $params
	 * @throws RequestException
	 * @throws Exception
	 * @return mixed
	 */
	public function handle_request(array $params = []) {
		if (!isset($params['type'])) {
			throw new RequestException('A type is required.');
		}

		if (!isset($params['loc'])) {
			throw new RequestException('A location is required.');
		}

		$start = $end = null;
		if (isset($params['start']) && strtotime($params['start'])) {
			$start = new DateTime($params['start'], new DateTimeZone('UTC'));
		}
		if (isset($params['end']) && strtotime($params['end'])) {
			$end = new DateTime($params['end'], new DateTimeZone('UTC'));
		}

		if (!isset($params['keyword'])) {
			throw new Exception("A keyword is required.");
		}

		$type = $params['type'];
		
		$page = @$params['page'] ? (int)$params['page'] : 1;
		$page = max(1, $page);
		
		switch($type) {
			case 'finditemsbykeywords':
				return $this->find_items_by_keywords($params['keyword'], $params['loc'], $start, $end, $page);
				break;
			case 'finditemscompletedbykeywords':
				return $this->find_items_completed_by_keywords($params['keyword'], $params['loc'], $start, $end, $page, @$params['filters'], @$params['sort']);
				break;
			case 'finditemstotalbykeywords':
				return $this->find_items_total_by_keywords($params['keyword'], $params['loc'], $start, $end);
				break;
			case 'finditemscompletedtotalbykeywords':
				return $this->find_items_completed_total_by_keywords($params['keyword'], $params['loc'], $start, $end);
				break;
			case 'findsolditemstotalbykeywords':
				return $this->find_sold_items_total_by_keywords($params['keyword'], $params['loc'], $start, $end);
				break;				
			case 'finditemsadvancedtotalbykeywords':
				return $this->find_items_advanced_total_by_keywords($params['keyword'], $params['loc'], $start, $end);
				break;
			case 'findcatgoriesbykeywords':
				return $this->find_catgories_by_keywords($params['keyword'], $params['loc'], $start, $end);
				break;
			case 'findcurrency':
				return $this->find_currency($params['keyword'], $params['loc'], $start, $end);
				break;
			default:
				throw new RequestException('A valid type is required');
		}
	}

	/**
	 *
	 * @param string $keyword
	 * @param string $loc
	 * @return array
	 */
	public function find_items_by_keywords($keyword, $loc, DateTime $start = null, DateTime $end = null, $page = 1) {
		$requestOptions = array();
		if ($start) {
			$requestOptions['itemFilter'][] = array(
				'name' => 'StartTimeFrom',
				'value' => $start->format(DateTime::ISO8601)
			);
		}
		if ($end) {
			$requestOptions['itemFilter'][] = array(
				'name' => 'StartTimeTo',
				'value' => $end->format(DateTime::ISO8601)
			);
		}
		if ($page > 1) {
			$requestOptions['paginationInput'][] = array(
				'pageNumber' => $page
			);
		}
		$data = $this->getEbayData($keyword, $loc, 'findItemsByKeywords', $requestOptions);
		$simplexml = new SimpleXMLElement($data);
		$items = array();
		foreach($simplexml->searchResult->item as $simplexml_item) {
			$items[] = $this->parseItem($simplexml_item);
		}
		return $items;
	}

	
	public function find_items_completed_by_keywords($keyword, $loc, DateTime $start = null, DateTime $end = null, $page = 1, $filters = array(), $sort = '') {
		$requestOptions = array();
		if ($start) {
			$requestOptions['itemFilter'][] = array(
				'name' => 'StartTimeFrom',
				'value' => $start->format(DateTime::ISO8601)
			);
		}
		if ($end) {
			$requestOptions['itemFilter'][] = array(
				'name' => 'StartTimeTo',
				'value' => $end->format(DateTime::ISO8601)
			);
		}
		if ($page > 1) {
			$requestOptions['paginationInput'][] = array(
				'pageNumber' => $page
			);
		}
		
		if(!empty($filters)) {
			foreach($filters as $filter) {
				$requestOptions['itemFilter'][] = $filter;
			}	
		}
		
		if(!empty($sort)) {
			$requestOptions['sortOrder'] = $sort;
		}
		
		$data = $this->getEbayData($keyword, $loc, 'findCompletedItems', $requestOptions);
		$simplexml = new SimpleXMLElement($data);
		$items = array();
		foreach($simplexml->searchResult->item as $simplexml_item) {
			$items[] = $this->parseItem($simplexml_item);
		}
		return $items;
	}
	
	
	
	/**
	 *
	 * @param string $keyword
	 * @param string $loc
	 * @return array
	 */
	public function find_items_total_by_keywords($keyword, $loc, DateTime $start = null, DateTime $end = null) {
		$requestOptions = array();
		if ($start) {
			$requestOptions['itemFilter'][] = array(
				'name' => 'StartTimeFrom',
				'value' => $start->format(DateTime::ISO8601)
			);
		}
		if ($end) {
			$requestOptions['itemFilter'][] = array(
				'name' => 'StartTimeTo',
				'value' => $end->format(DateTime::ISO8601)
			);
		}
		$data = $this->getEbayData($keyword, $loc, 'findItemsByKeywords', $requestOptions);
		$simplexml = new SimpleXMLElement($data);
		return array(
			'total' => (int) $simplexml->paginationOutput->totalEntries
		);
	}

	
	public function find_items_advanced_total_by_keywords($keyword, $loc, DateTime $start = null, DateTime $end = null) {
		$requestOptions = array();
		if ($start) {
			$requestOptions['itemFilter'][] = array(
				'name' => 'StartTimeFrom',
				'value' => $start->format(DateTime::ISO8601)
			);
		}
		if ($end) {
			$requestOptions['itemFilter'][] = array(
				'name' => 'StartTimeTo',
				'value' => $end->format(DateTime::ISO8601)
			);
		}
		$requestOptions['descriptionSearch'] = true;
		
		$data = $this->getEbayData($keyword, $loc, 'findItemsAdvanced', $requestOptions);
		$simplexml = new SimpleXMLElement($data);
		return array(
			'total' => (int) $simplexml->paginationOutput->totalEntries
		);
	}
	
	
	/**
	 *
	 * @param string $keyword
	 * @param string $loc
	 * @param DateTime $start
	 * @param DateTime $end
	 */
	public function find_items_completed_total_by_keywords($keyword, $loc, DateTime $start = null, DateTime $end = null) {
		$total = 0;
		$requestOptions = array();
		if ($start) {
			$requestOptions['itemFilter'][] = array(
				'name' => 'StartTimeFrom',
				'value' => $start->format(DateTime::ISO8601)
			);
		}
		if ($end) {
			$requestOptions['itemFilter'][] = array(
				'name' => 'StartTimeTo',
				'value' => $end->format(DateTime::ISO8601)
			);
		}

		$data = $this->getEbayData($keyword, $loc, 'findCompletedItems', $requestOptions);
		$simplexml = new SimpleXMLElement($data);
		return array(
				'total' => (int) $simplexml->paginationOutput->totalEntries
		);
	}
	
	public function find_sold_items_total_by_keywords($keyword, $loc, DateTime $start = null, DateTime $end = null) {
		$total = 0;
		$requestOptions = array();
		if ($start) {
			$requestOptions['itemFilter'][] = array(
				'name' => 'StartTimeFrom',
				'value' => $start->format(DateTime::ISO8601)
			);
		}
		if ($end) {
			$requestOptions['itemFilter'][] = array(
				'name' => 'StartTimeTo',
				'value' => $end->format(DateTime::ISO8601)
			);
		}
	
		$requestOptions['itemFilter'][] = array(
			'name' => 'SoldItemsOnly',
			'value' => true
		);
	
		$data = $this->getEbayData($keyword, $loc, 'findCompletedItems', $requestOptions);
		$simplexml = new SimpleXMLElement($data);
		return array(
			'total' => (int) $simplexml->paginationOutput->totalEntries
		);
	}		
	
	
	
	public function find_currency($keyword, $loc, DateTime $start = null, DateTime $end = null, $page = 1) {
		$currency = '';
		
		$requestOptions = array();
		if ($start) {
			$requestOptions['itemFilter'][] = array(
				'name' => 'StartTimeFrom',
				'value' => $start->format(DateTime::ISO8601)
			);
		}
		if ($end) {
			$requestOptions['itemFilter'][] = array(
				'name' => 'StartTimeTo',
				'value' => $end->format(DateTime::ISO8601)
			);
		}
		if ($page > 1) {
			$requestOptions['paginationInput'][] = array(
				'pageNumber' => $page
			);
		}
	
		$data = $this->getEbayData($keyword, $loc, 'findItemsAdvanced', $requestOptions);
		$simplexml = new SimpleXMLElement($data);
		
		// Find currency
		foreach($simplexml->searchResult->item as $item) {
			try {
				
				if(isset($item->sellingStatus->currentPrice) && $curentPrice = $item->sellingStatus->currentPrice) {
					$currency = (string) $curentPrice->attributes()->currencyId;
				}
				
				if(empty($currency)) {
					throw new Exception('Currency unavailable.');
				}
			} catch (Exception $e) {
				if(isset($item->listingInfo->buyItNowPrice) && $buyItNowPrice = $item->listingInfo->buyItNowPrice) {
					$currency = (string) $buyItNowPrice->attributes()->currencyId;
				}
				
				if(empty($currency)) {
					if(isset($item->discountPriceInfo->originalRetailPrice) && $originalRetailPrice = $item->discountPriceInfo->originalRetailPrice) {
						$currency = (string) $originalRetailPrice->attributes()->currencyId;
					}
				}
			}
		
			if(!empty($currency)) {
				break;
			} 

		}
		return compact('currency');
	}
	
	
	

	/**
	 *
	 * @param string $keyword
	 * @param string $loc
	 * @return array
	 */
	public function find_catgories_by_keywords($keyword, $loc, DateTime $start = null, DateTime $end = null) {
		$requestOptions = array('outputSelector' => 'CategoryHistogram');
		if ($start) {
			$requestOptions['itemFilter'][] = array(
				'name' => 'StartTimeFrom',
				'value' => $start->format(DateTime::ISO8601)
			);
		}
		if ($end) {
			$requestOptions['itemFilter'][] = array(
				'name' => 'StartTimeTo',
				'value' => $end->format(DateTime::ISO8601)
			);
		}
		$data = $this->getEbayData($keyword, $loc, 'findItemsByKeywords', $requestOptions);
		$simplexml = new SimpleXMLElement($data);
		$categories = array();
		if(!empty($simplexml->categoryHistogramContainer->categoryHistogram)) {
			foreach($simplexml->categoryHistogramContainer->categoryHistogram as $category) {
				if (!empty($category->childCategoryHistogram)) {
					foreach ($category->childCategoryHistogram as $childCategory) {
						$categories[] = $this->parseCategory($childCategory, $category);
					}
				} else {
					$categories[] = $this->parseCategory($category);
				}
			}
		}
		return $categories;
	}
	
	
	public function is_valid_response($response) {
		try {
			$simplexml = new SimpleXMLElement($response);
		} catch(Exception $e) {
			return false;
		}
		
		if ($simplexml->ack != 'Success') {
			if ($simplexml->error->category == 'System') {
				//the error was ebays fault, so retry
				return true;
			} else{
				return false;
				
			}
		}
		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see Module::should_retry_request()
	 */
	public function should_retry_request($response, $retry_options = []) {
		try {
			$simplexml = new SimpleXMLElement($response);
		} catch(Exception $e) {
			return true;
		}
		
		if ($simplexml->ack != 'Success') {
			if ($simplexml->error->category == 'System') {
				//the error was ebays fault, so retry
				return true;
			} else{
				//something we did resulted in an error from ebay
				throw new Exception($simplexml->asXML());
			}
		}
		
		return false;
	}
	
	
	public function getErrorFromResponse($response) {
		$message = '';
		try {
			$simplexml = new SimpleXMLElement($response);
		} catch(Exception $e) {
			return false;
		}
	
	
		if(!empty($simplexml->error->message)) {
			$message = $simplexml->error->message;
		}

		if ($simplexml->ack != 'Success') {
			if ($simplexml->error->category != 'System') {
				if (!empty($simplexml->errorMessage->error->message)) {
					$message = $simplexml->errorMessage->error->message;
				} else{
					//something we did resulted in an error from ebay
					$message = parent::getErrorFromResponse($simplexml->asXML());
				}
			}
		}
		return $message;
	}
	
	
	/**
	 *
	 * @param unknown $keyword
	 * @param unknown $loc
	 * @param unknown $operation
	 * @param unknown $requestOptions
	 * @return Ambigous <boolean, mixed>
	 */
	protected function getEbayData($keyword, $loc, $operation, $requestOptions = array()) {
		$requestData = $this->generateEbayRequest($keyword, $operation, $requestOptions);
		$cacheKey = sha1(print_r(compact('operation', 'keyword', 'loc', 'requestData'), true));
		
		$this->use_keys_set('production');

		$curlHeaders = array(
			'X-EBAY-SOA-SERVICE-NAME: FindingService',
			'X-EBAY-SOA-SERVICE-VERSION: 1.12.0',
			'X-EBAY-SOA-OPERATION-NAME: ' . $operation,
			'X-EBAY-SOA-GLOBAL-ID: ' . $this->get_global_id($loc),
			'X-EBAY-SOA-SECURITY-APPNAME: ' . $this->keys['appID'],
			'X-EBAY-SOA-REQUEST-DATA-FORMAT: XML',
			'Content-Type: text/xml;charset=utf-8',
		);

		$dataGetterParams = array(
			'cache_key' => $cacheKey,
			'data_sources' => $this->format_data_sources_for_data_getter(array("local", "live")),
			'url_getter_params' => array(
				'url' => $this->endpoint,
				'curl_options' => array(CURLOPT_HTTPHEADER => $curlHeaders),
				'post_data' => $requestData,
			)
		);
		return DataGetter::get_data_or_throw($this, $dataGetterParams);
	}

	/**
	 *
	 * @param unknown $keyword
	 * @param unknown $options
	 * @return string
	 */
	protected function generateEbayRequest($keyword, $operation, $options = array()) {
		$xml_request  = '<?xml version="1.0" encoding="utf-8"?>';
		$xml_request .= '<'.$operation.'Request xmlns="http://www.ebay.com/marketplace/search/v1/services">';
		$xml_request .= '<keywords>'. $keyword .'</keywords>';
		if (!empty($options)) {
			array_map(function($key, $value) use(&$xml_request) {
				foreach ((array) $value as $_value) {
					if (is_array($_value)) {
						if (isset($_value['name'], $_value['value'])) {
							$xml_request .= "<{$key}><name>{$_value['name']}</name><value>{$_value['value']}</value></{$key}>";
						} else {
							$xml_request .= "<{$key}>";
							foreach ($_value as $__key => $__value) {
								$xml_request .= "<{$__key}>{$__value}</{$__key}>";
							}
							$xml_request .= "</{$key}>";
						}
					} elseif (is_scalar($_value)) {
						$xml_request .= "<{$key}>{$_value}</{$key}>";
					}
				}
			}, array_keys($options), $options);
		}
		$xml_request .= '</'.$operation.'Request>';
		return $xml_request;
	}

	/**
	 *
	 * @param SimpleXMLElement $xmlItem
	 * @return array
	 */
	protected function parseItem(SimpleXMLElement $xmlItem) {
		$item = array();

		$item['itemId'] =       (int)$xmlItem->itemId;
		$item['viewItemURL'] =  (string)$xmlItem->viewItemURL;
		$item['galleryURL'] =   (string)$xmlItem->galleryURL;
		$item['title'] =        (string)$xmlItem->title;

		$item['listingInfo'] = array();
		$item['listingInfo']['listingType'] = (string)$xmlItem->listingInfo->listingType;
		$endtime = (string)$xmlItem->listingInfo->endTime;
		$item['listingInfo']['endTime'] = $this->ebay_time_to_timestamp($endtime);
		
		$startTime = (string)$xmlItem->listingInfo->startTime;
		$item['listingInfo']['startTime'] = $this->ebay_time_to_timestamp($startTime);
		

		$item['primaryCategory'] = array();
		$item['primaryCategory']['categoryId'] = (int)$xmlItem->primaryCategory->categoryId;

		$item['sellingStatus'] = array();
		$item['sellingStatus']['bidCount'] =               	(int)(string)$xmlItem->sellingStatus->bidCount;
		$item['sellingStatus']['currentPrice'] =  			(float)(string)$xmlItem->sellingStatus->currentPrice;
		$item['sellingStatus']['convertedCurrentPrice'] =  	(float)(string)$xmlItem->sellingStatus->convertedCurrentPrice;
		$item['sellingStatus']['timeLeft'] =               	$this->ebay_duration_to_seconds($xmlItem->sellingStatus->timeLeft);
		$item['sellingStatus']['sold'] =               	(string)$xmlItem->sellingStatus->sellingState == "EndedWithSales" ? true : false;


		$item['shippingInfo'] = array();
		$item['shippingInfo']['shippingServiceCost'] = (float)(string)$xmlItem->shippingInfo->shippingServiceCost;
		$item['shippingInfo']['shippingType'] =        (string)$xmlItem->shippingInfo->shippingType;

		return $item;
	}

	/**
	 *
	 * @param SimpleXMLElement $xmlCategory
	 * @param SimpleXMLElement $xmlParentCategory
	 * @return array
	 */
	protected function parseCategory(SimpleXMLElement $xmlCategory, SimpleXMLElement $xmlParentCategory = null) {
		$name = $fullName = "{$xmlCategory->categoryName}";
		if (isset($xmlParentCategory)) {
			$fullName = "{$xmlParentCategory->categoryName} > {$name}";
		}
		$category =  array(
			'category' => (int) $xmlCategory->categoryId,
			'category name' => $name,
			'category full name' => $fullName,
			'number of listings' => (int) $xmlCategory->count
		);
		return $category;
	}

}
