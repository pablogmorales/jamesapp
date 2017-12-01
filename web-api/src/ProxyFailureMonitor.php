<?php

namespace Daytalytics;


class ProxyFailureMonitor {

	/**
	 *
	 * @var unknown
	 */
	protected static $_db;

	/**
	 *
	 * @var unknown
	 */
	protected static $_params = array(
		'start' => '-1 day',
		'end' => 'now',
		'modules' => 'all'
	);

	/**
	 *
	 * @var unknown
	 */
	protected static $_defaultConfig = array(
		'threshold' => 5,
		'conditions' => null
	);

	/**
	 *
	 * @var unknown
	 */
	public static $moduleConfig = array();

	/**
	 * Analyze proxy failures from command line arguments
	 *
	 * @see ProxyFailureMonitor::_parseArgs()
	 * @see ProxyFailureMonitor::$_params
	 *
	 * @param unknown $args
	 * @return ProxyFailureResult
	 */
	public static function run($args) {
		$params = static::_parseArgs($args);
		extract($params);
		$tz = new \DateTimeZone('UTC');
		$startTime = new \DateTime($start, $tz);
		$endTime = new \DateTime($end, $tz);
		if ($modules !== 'all') {
			$modules = (array)$modules;
		} else {
			$modules = static::_getFailedModules($startTime, $endTime);
		}
		return static::analyze($modules, $startTime, $endTime);
	}

	/**
	 * Analyze proxy failures
	 *
	 * @param array $modules
	 * @param DateTime $startTime
	 * @param DateTime $endTime
	 * @return ProxyFailureResult
	 */
	public static function analyze(array $modules, \DateTime $startTime, \DateTime $endTime) {
		$day = 86400;
		$diff = $endTime->getTimestamp() - $startTime->getTimestamp();
		$multiplier = $diff / $day;
		$data = array();
		if (!empty($modules)) {
			foreach($modules as $module) {
				$config = static::_moduleConfig($module);
				$errors = static::_getProxyFailureCount($module, $startTime, $endTime, $config['conditions']);
				if ($errors) {
					foreach ($errors as $proxy_id => $errors) {
						//thresholds are configured daily, we autoscale to match the query period
						$threshold = max(round($config['threshold'] * $multiplier), 1);
						$failed = $errors > $threshold;
						$data[$proxy_id][$module] = compact('errors', 'failed') + $config;
					}
				}
			}
		}
		return new ProxyFailureResult(compact('modules', 'data', 'startTime', 'endTime'));
	}

	/**
	 *
	 * @param unknown $module
	 * @return Ambigous <number, unknown>
	 */
	protected static function _moduleConfig($module) {
		$config = static::$_defaultConfig;
		if (isset(static::$moduleConfig['default'])) {
			$config = static::$moduleConfig['default'] + $config;
		}
		if (isset(static::$moduleConfig[$module])) {
			$config = static::$moduleConfig[$module] + $config;
		}
		return $config;
	}

	/**
	 *
	 * @param unknown $module
	 * @param DateTime $startTime
	 * @param DateTime $endTime
	 * @param string $conditions
	 * @return multitype:unknown
	 */
	protected static function _getProxyFailureCount($module, \DateTime $startTime, \DateTime $endTime, $conditions = '') {
		if (empty($conditions)) {
			$conditions = '`status_code` >= 400 OR `module_detected_error` != false';
		}
		$count = array();
		$db = static::_db();
		$moduleSql = $db->escape_string($module);
		$sql = "
			SELECT COUNT(`id`) 'fails', `proxy_server_id`
			FROM `proxy_server_usage`
			WHERE (
				{$conditions}
			)
			AND `module_identifier` = '{$moduleSql}'
			AND `created` BETWEEN '{$startTime->format('Y-m-d H:i:s')}' AND '{$endTime->format('Y-m-d H:i:s')}'
			GROUP BY `proxy_server_id`
		";
		$result = $db->query($sql);
		if ($result = $db->query($sql)) {
			while ($row = $db->fetch_assoc($result)) {
				$count[$row['proxy_server_id']] = $row['fails'];
			}
		}
		return $count;
	}

	/**
	 *
	 * @param DateTime $startTime
	 * @param DateTime $endTime
	 * @return multitype:unknown
	 */
	protected static function _getFailedModules(\DateTime $startTime, \DateTime $endTime) {
		$sql = "
			SELECT DISTINCT(`module_identifier`)
			FROM `proxy_server_usage`
			WHERE (
				`status_code` >= 400 OR
		        `module_detected_error` != false
			) AND `created` BETWEEN '{$startTime->format('Y-m-d H:i:s')}' AND '{$endTime->format('Y-m-d H:i:s')}'
		";
		$modules = array();
		$db = static::_db();
		if ($result = $db->query($sql)) {
			while ($row = $db->fetch_assoc($result)) {
				$modules[] = $row['module_identifier'];
			}
		}
		if (!empty(static::$moduleConfig)) {
			foreach (static::$moduleConfig as $module => $_c) {
				if (in_array($module, $modules)) {
					continue;
				}
				$config = static::_moduleConfig($module);
				if (!empty($config['conditions'])) {
					$specialErrors = static::_getProxyFailureCount($module, $startTime, $endTime, $config['conditions']);
					if (!empty($specialErrors)) {
						$modules[] = $module;
					}
				}
			}
		}
		return $modules;
	}

	/**
	 *
	 * @param unknown $args
	 * @return string|Ambigous <unknown, unknown>
	 */
	protected static function _parseArgs($args) {
		$params = static::$_params;
		$short = array_map(function($p){
			return "{$p[0]}::";
		}, array_keys($params));
		$long = array_map(function($p){
			return "{$p}::";
		}, array_keys($params));
		$rawParams = getopt(implode('', $short), $long);
		foreach ($rawParams as $key => $value) {
			if (in_array("{$key}::", $short) || in_array("{$key}::", $long)) {
				$params[$key] = $value;
			}
		}
		return $params;
	}

	/**
	 *
	 * @param string $force
	 * @return unknown
	 */
	protected static function _db($force = false) {
		if (!isset(static::$_db) || $force) {
			static::$_db = Database::get_instance();
		}
		return static::$_db;
	}
}

?>
