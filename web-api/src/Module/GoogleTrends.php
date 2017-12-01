<?php

namespace Daytalytics\Module;

use Daytalytics\DataGetter;
use Daytalytics\ResponseCode;
use Daytalytics\RequestException;
use DateTime;
use DateTimeZone;

class GoogleTrends extends BaseModule {

	/**
	 * Nnumber of times this module can use each proxy in a day
	 *
	 * @var integer
	 */
	public $limit = 1000;

	/**
	 *
	 * @var string
	 */
	protected $keywordTrendUrl = 'http://www.google.com/trends/fetchComponent?hl=en-US&q=%s&cid=TIMESERIES_GRAPH_0&export=3&date=%s';

	public function define_service() {
		return [
		    'keywordtrenddata' => [
		        'parameters' => [
		            'keyword' => [
		                'description' => 'Keyword search term',
		                'required' => true
		            ],
		            'start' => [
		                'description' => 'Start time',
		                'format' => 'dateTime'
		            ],
		            'end' => [
		                'description' => 'End time',
		                'format' => 'dateTime'
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
		if(!isset($params['keyword'])) {
			throw new RequestException('Keyword is required.');
		}
		switch (@$params['type']) {
			case 'keywordtrenddata':
				$result = $this->keywordTrendData($params['keyword'], $params);
			break;
			default:
				throw new RequestException('A valid type is required');
			break;
		}
		return $result;
	}

	/**
	 *
	 * @param unknown $keyword
	 * @param unknown $options
	 * @throws RequestException
	 * @return unknown
	 */
	public function keywordTrendData($keyword, $options = array()) {
		$dates = $this->getQueryDates($options);
		$queryDate = '';
		$queryDate.= $dates->start->format('m/Y');
		$diff = $dates->end->diff($dates->start);
		
		// Using $diff->m is always 0 if date range is 2013-07-01 to 2015-07-01 basically with same month and day
		// Calculate month diff manually		
		$monthDiff = round($diff->days / 30);
		$queryDate.= " {$monthDiff}m";
		$requestUrl = sprintf($this->keywordTrendUrl, urlencode($keyword), urlencode($queryDate));
		
		$cacheKey = sha1($requestUrl);
		$readCache = !empty($this->data_sources['local']);
		if ($readCache) {
			$cacheData = $this->db->get_parsed_data($cacheKey, '-1 day', $this->identify());
			if ($cacheData && $parsedData = unserialize($cacheData)) {
				return $parsedData;
			}
		}

		$dataGetterParams = array(
			'cache_key' => $cacheKey,
			'data_sources' => $this->format_data_sources_for_data_getter(),
			'url_getter_params' => array(
				'url' => $requestUrl
			)
		);

		$rawData = DataGetter::get_data_or_throw($this, $dataGetterParams);
		
		if ($rawData && ($jsonString = $this->resultAsJson($rawData))) {
			$jsonData = json_decode($jsonString, true);
			
			if($jsonData['status'] == 'ok') {
				$parsedData = $this->parseRawKeywordTrendData($jsonData);
				$cacheData = serialize($parsedData);
				$this->db->set_parsed_data($cacheKey, $cacheData, $this->identify());
				return $parsedData;
			} else {
				$errors = array();
				if(!empty($jsonData['errors'])) {
					foreach($jsonData['errors'] as $error) {
						$errors[] = "{$error['message']}: {$error['detailed_message']}";
					}
					throw new RequestException(implode("\n", $errors), ResponseCode::EMPTY_RESULT);
				}
			}
		}

		throw new RequestException('Data not available.');
	}

	/**
	 *
	 * @param string $json
	 * @return mixed array | NULL
	 */
	protected function parseRawKeywordTrendData($jsonData) {
		if(is_string($jsonData)) {
			$jsonData = json_decode($jsonData, true);
		}
		
		if ($jsonData) {
			if (!empty($jsonData['table']['rows'])) {
				$rows = $jsonData['table']['rows'];
				$parsedData = array_map(array($this, 'parseRawKeywordTrendRow'), $rows);
				return $parsedData;
			}
		}
	}

	
	/**
	 * Cleanup result as a valid json string 
	 */
	protected function resultAsJson($rawData) {
		$search = array(
			'/^[^{]*/' => '',
			'/[^}]*$/' => '',
			'/new Date\((.*?)\)/' => '"$1"'
		);
		$jsonString = preg_replace(array_keys($search), $search, $rawData);
		return $jsonString;
	}
	
	
	/**
	 *
	 * @param array $rawDataRow
	 * @return array
	 */
	protected function parseRawKeywordTrendRow($rawDataRow) {
		// From string like "2014,11,7" w/c is a js format and uses a zero indexed month, e.g. 11 = December
		$date = explode(',', $rawDataRow['c'][0]['v']);
		$date = $date[0] . '-' . ($date[1] + 1) . '-' . $date[2];
		$value = @$rawDataRow['c'][1]['f'];
		$dateTime = new DateTime($date, new DateTimeZone('UTC'));
		$row = array(
			'date_unparsed' => $rawDataRow['c'][0]['f'],
			'time' => $dateTime->getTimestamp(),
			'date' => $dateTime->format('Y-m-d'),
			'interest' => $value
		); 
		return $row;
	}

	/**
	 *
	 * @param unknown $options
	 * @throws RequestException
	 * @return StdClass
	 */
	protected function getQueryDates($options) {
		$timezone = new DateTimeZone('UTC');
		if (!empty($options['end'])) {
			if (!($endTime = @strtotime($options['end']))) {
				throw new RequestException('Invalid end date.');
			}
			$end = new DateTime($options['end'], $timezone);
		} else {
			$end = new DateTime('now', $timezone);
		}

		if (!empty($options['start'])) {
			if (!($startTime = @strtotime($options['start']))) {
				throw new RequestException('Invalid start date.');
			}
			$start = new DateTime($options['start'], $timezone);
		} else {
			$start = clone $end;
			$start->modify('-1 month');
		}

		//end must not be past this month
		$nextMonth = new DateTime('next month', $timezone);
		$nextMonth->setDate($nextMonth->format('Y'), $nextMonth->format('m'), 1);
		$nextMonth->setTime(0, 0, 0);
		if ($end->getTimestamp() > $nextMonth->getTimestamp()) {
			throw new RequestException('Invalid end date.');
		}

		//start must be less than end
		if ($start->getTimestamp() > $end->getTimestamp()) {
			throw new RequestException('Invalid start date.');
		}

		//start month must be less than end month
		if ($start->format('Y') == $end->format('Y') && $start->format('m') >= $end->format('m')) {
			throw new RequestException('Invalid start date.');
		}
		return (object) compact('start', 'end');
	}
}
?>