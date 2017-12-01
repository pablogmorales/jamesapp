<?php

namespace Daytalytics\Module;

use Daytalytics\DataGetter;
use Daytalytics\RequestException;
use Daytalytics\AuthToken;
use SimpleXMLElement;
use Exception;

class MajesticSeo extends BaseModule {

    /*  We don't need to hide from Majestic - so don't use a proxy to get the data */
    protected $useProxies = false;
    
	private $endPoint = 'https://api.majestic.com/api/xml';

	const API_KEY = '';
	
	public function construct() {
		// Set developer endpoint to save some credits ($)
		if (getenv('APP_ENVIRONMENT') != 'production') {
			$this->endPoint = 'https://developer.majestic.com/api/xml';
		}
	}
	
	public function define_service() {
	    
	    
	    return [
	        'backlinks' => [
	            'parameters' => [
	                'url' => [
	                    'description' => 'The subject url',
	                    'required' => true
	                ],
	                'datasource' => [
	                    'description' => 'Data source',
	                    'default' => 'historic',
	                    'options' => ['historic', 'fresh']
	                ]
	            ]
	        ],
	        'bllist' => [
	            'parameters' => [
	                'url' => [
	                    'description' => 'The subject url',
	                    'required' => true
	                ],
	                'datasource' => [
	                    'description' => 'Data source',
	                    'default' => 'historic',
	                    'options' => ['historic', 'fresh']
	                ],
	                'count' => [
	                    'description' => 'Number of results',
	                    'type' => 'number',
	                    'minimum' => 50,
	                    'maximum' => 1000,
	                    'required' => true
	                ]
	            ]
	        ]
	    ];
	}

	public function handle_request(array $params = []) {
		if (empty($params['type'])){
			throw new RequestException('Invalid type');
		};

		if (!empty($params['datasource'])){
			$datasource = $params['datasource'];
			$ds = array('historic', 'fresh');
			if (!in_array($datasource, $ds)){
				throw new RequestException('Invalid parameter datasource.');
			};
		}else{
			$datasource = 'historic';
		};

		if (!empty($params['type'])){
			$type = $params['type'];
			$types = array('backlinks', 'bllist');
			if (!in_array($type, $types)){
				throw new RequestException('Invalid parameter type.');
			};
		}else{
			$type = 'backlinks';
		};

		if ($type == 'bllist') {
			if (!empty($params['count'])){
				$count = $params['count'];
				if (($count < 50) || ($count > 1000)){
					throw new RequestException('Invalid parameter count.');
				};
			}else{
				throw new RequestException('Invalid parameter count.');
			};
		};

		switch ($type) {
			case 'backlinks':
				if (empty($params['url']))
					throw new RequestException('Invalid url');

				return $this->get_backlinks($params['url'], $datasource);
				break;
			case 'bllist':
				if (empty($params['url']))
					throw new RequestException('Invalid url');

				return $this->get_backlinks_list($params['url'], $datasource, $count);
				break;
			default:
				throw new RequestException("Invalid type");
				break;
		}
	}

	function get_backlinks($url, $datasource) {
		$dg_params = array(
			'cache_key' => "$url:$datasource:blcount",
			'cache_expiry_time' => "-14 days",
			'data_sources' => $this->format_data_sources_for_data_getter(array("local", "live")),
			'url_getter_params' => array(
				'url' => $this->get_backlinks_url($url, $datasource),
			),
		);
		$data = DataGetter::get_data_or_throw($this, $dg_params);
		return $this->parse_backlink_data($data);
	}

	function get_backlinks_list($url, $datasource, $aCount) {
		$dg_params = array(
			'cache_key' => "$url:$datasource:bllist:$aCount",
			'cache_expiry_time' => "-14 days",
			'data_sources' => $this->format_data_sources_for_data_getter(array("local", "live")),
			'url_getter_params' => array(
				'url' => $this->get_backlinks_list_url($url, $datasource, $aCount),
			),
		);
		$data = DataGetter::get_data_or_throw($this, $dg_params);
		return $this->parse_backlink_list_data($data);
	}

	function parse_backlink_data($data) {
		$xml = new SimpleXMLElement($data);
		if ($xml['Code'] != 'OK') {
		if(AuthToken::find_from_request()->can_view_errors) {
				throw new RequestException("An error occured in the majestic API: {$xml['Code']} - {$xml['ErrorMessage']}");
			} else {
				throw new RequestException('Data not available.');
			}
		}
		
		$data_values = (String)$xml->DataTables->DataTable->Row;

		//Pipe chars in the data are returned as 2 adjacent pipe chars. Empty
		//data cells always contain a space so if there are 2 directly adjacent
		//pipes then it is in the data - not the data formatting. Sub out pipes
		//for a magic flag, then sub them back in later.
		$data_values = str_replace('||', '%%%', $data_values);

		$data_values = explode("|", $data_values);
		$data_values = array_map("trim", $data_values);

		foreach($data_values as $data_value) {
			$data_values = str_replace('%%%', '|', $data_values);
		}

		$data_keys = (String)$xml->DataTables->DataTable["Headers"];
		$data_keys = explode("|", $data_keys);

		return array_combine($data_keys, $data_values);
	}

	function get_backlinks_url($url, $datasource) {
		$params = [
			'app_api_key' => self::API_KEY,
			'cmd' => 'GetIndexItemInfo',
			'items' => 1,
			'item0' => $url,
			'datasource' => $datasource
		];
		
		return $this->endPoint . "?" . http_build_query($params);
	}

	function get_backlinks_list_url($url, $datasource, $aCount) {
		$params = [
			'app_api_key' => self::API_KEY,
			'cmd' => 'GetBackLinkData',
			'item' => $url,
			'datasource' => $datasource,
			'Count' => $aCount,
			'MaxSameSourceURLs' => 1
		];
		
		return $this->endPoint . "?" . http_build_query($params);
	}

	function parse_backlink_list_data($data) {
		$xml = new SimpleXMLElement($data);
		if ($xml['Code'] != 'OK') {
			if(AuthToken::find_from_request()->can_view_errors) {
				throw new RequestException("An error occured in the majestic API: {$xml['Code']} - {$xml['ErrorMessage']}");
			} else {
				throw new RequestException('Data not available.');
			}
		}

		$data_keys = (String)$xml->DataTables->DataTable["Headers"];
		$data_keys = explode("|", $data_keys);
		$data_keyvalues = [];

		//Pipe chars in the data are returned as 2 adjacent pipe chars. Empty
		//data cells always contain a space so if there are 2 directly adjacent
		//pipes then it is in the data - not the data formatting. Sub out pipes
		//for a magic flag, then sub them back in later.
		for ($i = 0; $i < $xml->DataTables->DataTable->Row->count(); $i++){
			$data_value = (String)$xml->DataTables->DataTable->Row[$i];
			$data_value = str_replace('||', '%%%', $data_value);
			$data_value = explode("|", $data_value);

			for ($j = 0; $j < count($data_value); $j++){
				$data_value[$j] = str_replace('%%%', '|', $data_value[$j]);
			}
			$data_keyvalue = array_combine($data_keys, $data_value);

			$data_keyvalues[$i] = $data_keyvalue;
		};

		$data_attributes = array(
			'RowsCount'=> (String)$xml->DataTables->DataTable['RowsCount'],
			'AvailableLines'=> (String)$xml->DataTables->DataTable['AvailableLines'],
			'TotalBackLinks'=> (String)$xml->DataTables->DataTable['TotalBackLinks'],
			'Count'=> (String)$xml->DataTables->DataTable['Count'],
			'From'=> (String)$xml->DataTables->DataTable['From'],
			'Item'=> (String)$xml->DataTables->DataTable['Item'],
			'ItemType'=> (String)$xml->DataTables->DataTable['ItemType'],
			'OrigItem'=> (String)$xml->DataTables->DataTable['OrigItem'],
		);

		return array('info'=>$data_attributes, 'backlink'=>$data_keyvalues);
	}
}
