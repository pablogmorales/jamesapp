<?php

namespace Daytalytics;

use Exception;

class ProxyServer {
	private static $db;
	private static $default_proxy = array('id'=>'0', 'path'=>'self');

	public static function proxify_url($url, $proxy) {
		$proxy_query = $url;
		if("base64")
			$proxy_query = rawurlencode(base64_encode($url)).'&base64=1';
		else
			$proxy_query = rawurlencode($url);
		$proxy_query = '?id=' . $proxy['id'] . '&url=' . $proxy_query;

		return getenv('APP_BASE_URL') . "proxy.php" . $proxy_query;
	}

	/**
	 * @returns a url to call a socket proxy
	 */
	public static function proxify_socket($host, $port, $request, $proxy_path) {
		$proxy_query = '?host=' . urlencode($host) . '&port=' . urlencode($port);
		
		$time = time();
		$proxy_query .= '&time=' . urlencode($time);
		$proxy_host = parse_url($proxy_path, PHP_URL_HOST);
		$hash = sha1($time . $request . $time . $proxy_host);
		$proxy_query .= '&hash=' . urlencode($hash);
		
		$url = $proxy_path . $proxy_query;
		return $url;
	}

	/**
	 * This should return proxies from the database.
	 */
	public static function get_proxies() {
		$res = self::get_db()->query('SELECT * FROM `proxy_servers` WHERE enabled="1"');
		if ($res === false) {
		    return [self::get_default_proxy()];
		}
		$proxies = array();
		while($row = self::get_db()->fetch_assoc($res)) {
			$proxies[] = $row;
		}
		if(empty($proxies)) {
		    return [self::get_default_proxy()];
		}
		return $proxies;
	}

	/**
	 * Add a usage row for a proxy and a module. The goal of this is to not
	 * use the same proxy too often for a website.
	 * Usually don't use $other_identifier. It is only when a module needs more than 1 limit.
	 
	 * Note: Because this is delayed, it is possible for a few more uses to occur after reaching
	 * a limit before get_usage() will realize. (i.e. the last few uses weren't registered yet,
	 * even though they occurred)
	 */
	public static function track_usage($proxy_id, $module_identifier, $http_code, $module_detected_error, $other_identifier='') {
		$proxy = self::get_proxy($proxy_id);
		if(empty($proxy)) {
			return false;
		}


		$q = 'INSERT DELAYED INTO `proxy_server_usage` SET created="'.date('Y-m-d H:i:s').'", proxy_server_id="'.((int)$proxy['id']).'", module_identifier="'.self::get_db()->escape_string($module_identifier).'", other_identifier="'.self::get_db()->escape_string($other_identifier).'", status_code="'.((int)$http_code).'", module_detected_error="'.((bool)$module_detected_error).'"';

		self::get_db()->unbuffered_query($q);
	}

	/**
	 * @name get_usage()
	 * @description get the number of times a proxy server have been used by
	 * the specified module
	 *
	 * NOT YET IMPLEMENTED:
	 * $other_identifier will eventually is provided in case a single module
	 * uses different limits for different actions, and hence needs to separate
	 * the usage tracking. e.g. GoogleGetter 1.0, PageRank; GoogleGetter 1.0, NS
	 * The usage for pagerank may be tracked separately to that of a natural search.
	 * 
	 * When this is implemented, update the index on the db table to ensure the
	 * WHERE doesn't cause a slow query
	 */
	public static function get_usage($proxy_id, $module_identifier, $other_identifier='', $age='-1 day') {
		$date = strtotime($age);
		if($date === false)
			throw new Exception('Invalid age/date.');
		$date = date('Y-m-d H:i:s', $date);
		
		$proxy = self::get_proxy($proxy_id);
		if(empty($proxy))
			return false;
		
		if ($other_identifier !== '')
			throw new Exception('Support for other identifier is not yet implemented.');
		
		$res = self::get_db()->unbuffered_query('SELECT SQL_NO_CACHE SQL_CALC_FOUND_ROWS id FROM `proxy_server_usage` WHERE module_identifier="'.self::get_db()->escape_string($module_identifier).'" AND proxy_server_id="'.((int)$proxy['id']).'" AND created > "'.self::get_db()->escape_string($date).'"');
		$res = self::get_db()->query('SELECT FOUND_ROWS() as count');
		
		$row = self::get_db()->fetch_assoc($res);
		if($row === false)
			return false;
		return (int)$row['count'];
	}

	public static function get_default_proxy() {
		return self::$default_proxy;
	}

	public static function get_proxy_stats_for_module($module_id) {
		//an array of proxyid => array("successes"=>X, "fails"=>Y)
		$stats = array();


		$query = "SELECT `proxy_server_id`, count(*) as NumErrors
		          FROM `proxy_server_usage`
		          WHERE (`status_code` >= 400 OR
		                `module_detected_error` != false) AND
		                `created` > (NOW() - INTERVAL 1 day) AND
		                `module_identifier` = '$module_id'
		          GROUP BY `proxy_server_id`";
		$fails = self::get_db()->query($query);

		while ($row = self::get_db()->fetch_assoc($fails)) {
			$p_id = $row['proxy_server_id'];
			$stats[$p_id]['fails'] = $row['NumErrors'];
		}


		$query = "SELECT `proxy_server_id`, count(*) as NumSuccesses
		          FROM `proxy_server_usage`
		          WHERE `status_code` < 400 AND
		                `module_detected_error` = false AND
		                `created` > (NOW() - INTERVAL 1 day) AND
		                `module_identifier` = '$module_id'
		          GROUP BY `proxy_server_id`";
		$successes = self::get_db()->query($query);

		while ($row = self::get_db()->fetch_assoc($successes)) {
			$p_id = $row['proxy_server_id'];
			$stats[$p_id]['successes'] = $row['NumSuccesses'];
		}

		foreach($stats as &$stat) {
			if (empty($stat['fails']))
				$stat['fails'] = 0;

			if (empty($stat['successes']))
				$stat['successes'] = 0;
		}

		return $stats;
	}

	/**
	 * Get a full proxy row form the db given it's id, or full path
	 */
	public static function get_proxy($proxy) {
		$query = 'SELECT * FROM `proxy_servers` WHERE ';
		if(is_numeric($proxy))
			$query .= 'id="'.((int)$proxy).'"';
		else
			$query .= 'path="'.self::get_db()->escape_string($proxy).'"';
		$query .= ' LIMIT 1';
		
		$res = self::get_db()->query($query);

		$row = self::get_db()->fetch_assoc($res);
		return $row;
	}


	private static function get_db() {
		return WebInformationApi::get_instance()->db;
	}
	
	public static function has_recent_errors($proxy_id, $module_identifier, $min_num_errors = 5) {
		$proxy_id_sql_safe = (int)$proxy_id;
		$module_identifier_sql_safe = self::get_db()->escape_string($module_identifier);
		$since_date_sql_safe = date('Y-m-d H:i:s', strtotime('-2 hours'));
		$min_num_errors_sql_safe = (int)$min_num_errors;
		if ($min_num_errors_sql_safe < 1) {
			throw new Exception('Must check for at least 1 error.');
		}
		$query = "SELECT COUNT(*) as num_errors
		          FROM proxy_server_usage
		          WHERE `proxy_server_id` = $proxy_id_sql_safe
				  AND `module_identifier` = '$module_identifier_sql_safe'
				  AND `created` > '$since_date_sql_safe'
				  AND `status_code` IS NOT NULL AND `status_code` != 200
				  LIMIT $min_num_errors_sql_safe
				  ";
		$res = self::get_db()->query($query);
		$row = self::get_db()->fetch_assoc($res);
		$errors = (int)$row['num_errors'];
		
		return $errors >= $min_num_errors_sql_safe;
	}

}
