<?php

namespace Daytalytics\Module;

use Daytalytics\RequestException;
use DateTime;
use DateTimeZone;

class EbayResearchApiAlt extends EbayApi {

	public function define_service() {
	    return [
	        'researchresults' => [
	            'parameters' => [
                    'keyword' => [
                        'description' => 'Keyword search term',
                        'required' => true
                    ],
                    'start' => [
                        'description' => 'Defaults to latest possible. Must be over 30 days old. e.g. 2009-08-01',
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
		if (!isset($params['type'])) {
		    throw new RequestException('A valid type is required');
		}
		
		$params['loc'] = 'us';

		if (!isset($params['start'])) {
			$params['start'] = false;
		}

        try {
            switch (strtoupper($params['type'])) {
                case 'RESEARCHRESULTS' :
                    $results = $this->research_keyword($params['keyword'], $params['start']);
                    break;
                default:
                    throw new RequestException('A valid type is required');
                    break;
            }
        } catch (RequestException $e) {
            if ($headers = $e->getHeaders()) {
                $this->handleEbayException($headers);
            }
            throw $e;
        }

		return $results;
	}

	/**
	 *
	 * @param string $keyword
	 * @param mixed $start
	 * @return array
	 */
	public function research_keyword($keyword, $start = false) {
		$dates = $this->getQueryDates($start);
		$totals = $this->getResearchKeywordTotals($keyword, $dates->start, $dates->end);
		$result = array(
			'total' => $totals
		);
		return $result;
	}

	/**
	 *
	 * @param string $keyword
	 * @param DateTime $start
	 * @param DateTime $end
	 * 
	 * @return array
	 */
	protected function getResearchKeywordTotals($keyword, DateTime $start = null, DateTime $end = null) {
		$_params = array();
		if($start) {
			$_params['start'] = $start->format('Y-m-d');
		}
		if($end) {
			$_params['end'] = $end->format('Y-m-d');
		}

		// Listings
		$params = array(
			'module' => 'EbayFindingApi',
			'type' => 'FindItemsAdvancedTotalByKeywords',
			'keyword' => $keyword,
			'loc' => 'us'
		) + $_params;
		$totalListings = $this->module_to_module_request($params);
		
		// Sold listings
		$params = array(
			'module' => 'EbayFindingApi',
			'type' => 'FindSoldItemsTotalByKeywords',
			'keyword' => $keyword,
			'loc' => 'us'
		) + $_params;
		$totalSuccessfulListings = $this->module_to_module_request($params);

		// Completed listings
		$params = array(
			'module' => 'EbayFindingApi',
			'type' => 'FindItemsCompletedTotalByKeywords',
			'keyword' => $keyword,
			'loc' => 'us'
		) + $_params;
		$totalCompletedListings = $this->module_to_module_request($params);
		
		$totals = array(
			'number of listings' => $totalListings['total'],
			'number of successful listings' => $totalSuccessfulListings['total'],
			'number of completed listings' => $totalCompletedListings['total'],
			'number of total listings' => $totalListings['total'] + $totalSuccessfulListings['total'],

		);
		return $totals;
	}
	
	/**
	 *
	 * @param mixed $start
	 * @throws RequestException
	 * @return StdClass
	 */
	protected function getQueryDates($start) {
		$latest_start_date = date("Y-m-01", strtotime('-1 month'));
		if ($start === false) {
			$requested_start_date = $latest_start_date;
		} else {
			$requested_start_time = strtotime($start);
			if ($requested_start_time === false) {
				throw new RequestException('Invalid start date.');
			}
			$requested_start_date = date("Y-m-01", $requested_start_time);
			// A start date must be the beginning of the month, to keep our data modular (i.e. units of 1 month)
			if ($requested_start_date !== date("Y-m-d", $requested_start_time)) {
				throw new RequestException('Invalid start date.');
			}
			if (strtotime($requested_start_date) > strtotime($latest_start_date)) {
				throw new RequestException("Start date must be older than {$latest_start_date}.");
			}
		}
		$time = new DateTimeZone('UTC');
		$end = new DateTime('now', $time);
		$end->setTimestamp(strtotime('+1 month', strtotime($requested_start_date)));
		$start = new DateTime($requested_start_date, $time);
		return (object) compact('start', 'end');
	}
}
