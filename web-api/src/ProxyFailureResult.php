<?php

namespace Daytalytics;


class ProxyFailureResult {
	/**
	 *
	 * @var DateTime
	 */
	public $startTime;

	/**
	 *
	 * @var DateTime
	 */
	public $endTime;

	/**
	 *
	 * @var array
	 */
	public $modules = array();

	/**
	 *
	 * @var array
	 */
	public $data = array();

	/**
	 *
	 * @param array $params
	 */
	public function __construct($params = array()) {
		foreach ($params as $key => $value) {
			if (property_exists($this, $key)) {
				$this->{$key} = $value;
			}
		}
	}

	/**
	 * Get list of proxies that have reached threshold on at least one module
	 */
	public function failed() {
		$failed = array();
		foreach ($this->data as $proxy_id => $modules) {
			foreach ($modules as $module => $data) {
				if (!empty($data['failed'])) {
					$failed[$proxy_id][$module] = $data;
				}
			}
		}
		return $failed;
	}
}

?>