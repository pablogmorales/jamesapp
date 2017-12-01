<?php

namespace Daytalytics\Module;

use Daytalytics\DataGetter;
use Daytalytics\RequestException;

class SemRush extends BaseModule {

	const API_KEY = '';
	
	const API_ENPOINT = 'http://api.semrush.com';
	
	const DEFAULT_LIMIT = 10;	
	
	private $db_names = array("us", "uk", "ru", "de", "fr", "es", "it", "br", "ca", "au");

	public function define_service() {
	    $params = [
	        'database' => [
	            'description' => 'Keyword database to use',
	            'options' => $this->db_names,
	            'required' => true
	        ],
	        'keyword' => [
	            'description' => 'Keyword search term',
	            'required' => true
	        ],
	        'offset' => [
	            'description' => 'Position to seek to in result list',
	            'type' => 'number',
	            'default' => 0
	        ],
	        'limit' => [
	            'description' => 'Number of results to return',
	            'type' => 'number',
	            'default' => self::DEFAULT_LIMIT
	        ]
	    ];
	    
	    return [
	        'keyword_report' => [
	            'parameters' => [
	                'database' => $params['database'],
	                'keyword' => $params['keyword']
	            ]
	        ],
	        'related_keywords' => [
	            'parameters' => $params
	        ],
	        'phrase_fullsearch' => [
	            'parameters' => $params
	        ]
	    ];
	}

	public function handle_request(array $params = []) {
		if (empty($params['database']) || !in_array($params['database'], $this->db_names))
			throw new RequestException("db_name is invalid");

		if (empty($params['keyword']))
			throw new RequestException("Invalid keyword");

		if (empty($params['type']))
			throw new RequestException("Invalid type");

		switch ($params['type']) {
			case 'keyword_report':
				return $this->get_keyword_report($params['database'], $params['keyword']);
				break;
			case 'related_keywords':
				return $this->get_related_keywords_report($params['database'], $params['keyword'], $params);
				break;
			case 'phrase_fullsearch':
				return $this->get_full_search_keywords_report($params['database'], $params['keyword'], $params);
				break;
			default:
				throw new RequestException("Invalid type");
				break;
		}
	}

	private function get_related_keywords_report($db_name, $keyword, $params) {
		$offset = !empty($params['offset']) ? $params['offset'] : 0;
		$limit = !empty($params['limit']) ? $params['limit'] : self::DEFAULT_LIMIT;

		$cache_key = "$db_name:$keyword:related_keywords_report:$offset:$limit";

		$dg_params = array(
				'cache_key' => $cache_key,
				'data_sources' => $this->format_data_sources_for_data_getter(array("local", "live")),
				'url_getter_params' => array(
						'url' => $this->related_keywords_report_url_for($db_name, $keyword, $offset, $limit),
				),
		);
		$csv_data = DataGetter::get_data_or_throw($this, $dg_params);

		return $this->parse_related_keywords_report_data($csv_data);
	}

	function parse_related_keywords_report_data($csv_data) {
		$data = explode("\r\n", $csv_data);
		if(count($data) <= 1) {
			$data = explode("\n", $csv_data);
		}
		
		array_walk($data, create_function('&$csv_data', '$csv_data = str_getcsv($csv_data, ";");'));

		//chop off the headers
		$data = array_slice($data, 1);

		$to_return = array();
		foreach($data as $d) {
			$to_return[] = array(
				'phrase' => $d[0],
				'average_num_queries_per_month' => $d[1],
				'average_adword_cost_per_click' => $d[2],
				'advertising_competition' => $d[3],
				'num_pages' => $d[4],
				'trend_data' => $d[5],
			);
		}

		return $to_return;
	}

	private function get_keyword_report($db_name, $keyword) {
		$dg_params = array(
			'cache_key' => "$db_name:$keyword:keyword_report",
			'data_sources' => $this->format_data_sources_for_data_getter(array("local", "live")),
			'url_getter_params' => array(
				'url' => $this->keyword_report_request_url_for($db_name, $keyword),
			),
		);
		$csv_data = DataGetter::get_data_or_throw($this, $dg_params);

		return $this->parse_keyword_report_data($csv_data);
	}

	function parse_keyword_report_data($csv_data) {
		$data = explode("\r\n", $csv_data);
		if(count($data) <= 1) {
			$data = explode("\n", $csv_data);
		}
		
		array_walk($data, create_function('&$csv_data', '$csv_data = str_getcsv($csv_data, ";");'));
		$to_return = array(
			'phrase' => $data[1][0],
			'average_num_queries_per_month' => $data[1][1],
			'average_adword_cost_per_click' => $data[1][2],
			'advertising_competition' => $data[1][3],
			'num_pages' => $data[1][4],
			'trend_data' => $data[1][5],
		);

		return $to_return;
	}

	function related_keywords_report_url_for($db_name, $keyword, $offset, $limit) {
		$limit = $offset + $limit;
		$params = [
			'key' => self::API_KEY,
			'database' => $db_name,
			'export_columns' => 'Ph,Nq,Cp,Co,Nr,Td',
			'type' => 'phrase_related',
			'phrase' => $keyword,
			'display_offset' => $offset,
			'display_limit' => $limit
		];
		return self::API_ENPOINT . '?' . http_build_query($params);
		
	}

	function keyword_report_request_url_for($db_name, $keyword) {
		$params = [
			'key' => self::API_KEY,
			'database' => $db_name,
			'export_columns' => 'Ph,Nq,Cp,Co,Nr,Td',
			'type' => 'phrase_this',
			'phrase' => $keyword,
		];
		return self::API_ENPOINT . '?' . http_build_query($params);
	}

	private function get_full_search_keywords_report($db_name, $keyword, $params) {
		$offset = !empty($params['offset']) ? $params['offset'] : 0;
		$limit = !empty($params['limit']) ? $params['limit'] : self::DEFAULT_LIMIT;

		$cache_key = "$db_name:$keyword:get_full_search_keywords_report:$offset:$limit";

		$dg_params = array(
			'cache_key' => $cache_key,
			'data_sources' => $this->format_data_sources_for_data_getter(array("local", "live")),
			'url_getter_params' => array(
				'url' => $this->full_search_keywords_report_url_for($db_name, $keyword, $offset, $limit),
			),
		);

		$csv_data = DataGetter::get_data_or_throw($this, $dg_params);

		return $this->parse_full_search_keywords_report_data($csv_data);
	}

	function parse_full_search_keywords_report_data($csv_data) {
		$data = explode("\r\n", $csv_data);
		array_walk($data, create_function('&$csv_data', '$csv_data = str_getcsv($csv_data, ";");'));

		//chop off the headers
		$data = array_slice($data, 1);

		$to_return = array();
		foreach($data as $d) {
			$to_return[] = array(
				'phrase' => $d[0],
				'average_num_queries_per_month' => $d[1],
				'average_adword_cost_per_click' => $d[2],
				'advertising_competition' => $d[3],
				'num_pages' => $d[4],
				'trend_data' => $d[5],
			);
		}

		return $to_return;
	}

	function full_search_keywords_report_url_for($db_name, $keyword, $offset, $limit) {
		$params = [
			'key' => self::API_KEY,
			'database' => $db_name,
			'export_columns' => 'Ph,Nq,Cp,Co,Nr,Td',
			'type' => 'phrase_fullsearch',
			'phrase' => $keyword,
			'display_offset' => $offset,
			'display_limit' => $limit
		];
		return self::API_ENPOINT . '?' . http_build_query($params);
	}
}
