<?php

require_once (__DIR__ . '/opensrs/openSRS_loader.php');
require_once (__DIR__ . '/opensrs_logger.php');


/**
 * Convenience wrapper for OpenSRS API calls
 *
 */
class OpensrsApi {

	/**
	 * Make a request against the API
	 *
	 * @param array $callArray
	 * @return openSRS_base endpoint object instance of base class
	 */
	public static function request(array $callArray) {
		OpensrsLogger::start($callArray);
        $result = processOpenSRS('array', $callArray);
        OpensrsLogger::end();
        return $result;
	}

	/**
	 *
	 * @param string $method
	 * @param array $params
	 * @return openSRS_base
	 */
	public static function __callStatic($method, $params) {
		$callArray = array (
			'func' => $method
		);
		if (!empty($params[0])) {
			$callArray['data'] = $params[0];
		}
		return static::request($callArray);
	}

	/**
	 *
	 * @param unknown $method
	 * @param unknown $params
	 * @return openSRS_base
	 */
	public function __call($method, $params) {
		return static::__callStatic($method, $params);
	}
}

?>