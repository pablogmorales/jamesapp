<?php

namespace Daytalytics;

use \Exception;

class DataGetter {

	private $module;
	private $data_sources;
	private $cache_key;
	private $cache_expiry_time;

	private $url_getter_params;
	private static $final_datasource;
	
	public function __construct($module, $params) {
		$this->module = $module;
		$this->validate_params($params);
		$this->extract_params($params);
	}

	public static function get_data($module, $params) {
		$dg = new DataGetter($module, $params);
		return $dg->_get_data();
	}

	public static function get_data_or_throw($module, $params) {
		$data = DataGetter::get_data($module, $params);

		if ($data === false) {
			throw new RequestException("Data Unavailable. Try again later.", 503);
		}

		return $data;
	}

	private function _get_data() {
		//TODO: eliminate all accesses of $this->module except 
		//is_request_similar()

		$data = false;

		foreach ($this->data_sources as $data_source) {

			if($data_source == 'local') {
				$data = $this->get_data_from_cache();
			}

			elseif($data_source == 'live') {
				$data = $this->get_live_data();
			}

			//stop when one of the data sources succeeds
			if ($data !== false) {
				if(is_null(static::$final_datasource)) {
					static::$final_datasource = $data_source;
				}
				break;
			}
		}

		return $data;
	}

	private function get_data_from_cache() {
		return $this->module->db->get_raw_data($this->cache_key, $this->cache_expiry_time, $this->module->identify());
	}

	private function get_live_data() {
		$ug = new UrlGetter($this->module, $this->url_getter_params);
		$data = $ug->get();
		//only write to cache if response is valid
		if($this->module->is_valid_response($data)) {
			$this->module->db->set_raw_data($this->cache_key, $ug->get_request_url(), $data, $this->module->identify(), time());
		}
		return $data;
	}

	private function validate_params($params) {
		if (empty($params['cache_key'])) {
			throw new Exception("A cache key must be specified.");
		}

		if (empty($params['data_sources'])) {
			throw new Exception("Data sources must be specifed.");
		}
	}

	private function extract_params($params) {
		$this->cache_key = $params['cache_key'];
		$this->data_sources = $params["data_sources"];
		$this->url_getter_params = $params['url_getter_params'];

		if (empty($params['cache_expiry_time'])) {
			$this->cache_expiry_time = '-1 days';
		} else {
			$this->cache_expiry_time = $params["cache_expiry_time"];
		}
	}
	
	public static function get_final_datasource() {
		return static::$final_datasource;
	}

}
