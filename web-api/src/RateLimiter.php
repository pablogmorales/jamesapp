<?php

namespace Daytalytics;

use Daytalytics\Module\BaseModule;
use Zend\Diactoros\ServerRequest;

class RateLimiter {
	private $db;
	private $module;
	private $auth_token;
	private $ip;

	//You can use floating point numbers here
	private $limit_modifiers = array(
			"traffictravispro20130403" => 2
		);

	public function __construct($module, $auth_token, $ip) {
		$this->db = WebInformationApi::get_instance()->db;
		$this->module = $this->db->escape_string($module);
		$this->auth_token = $this->db->escape_string($auth_token);
		$this->ip = $ip;
	}

	public static function go(ServerRequest $request, AuthToken $authToken, BaseModule $module) {
	    $name = $module->name();
	    $server = $request->getServerParams();
	    $module = @$params['module'] ?: '';
	    $token = $authToken->token;
	    $ip = @$server['REMOTE_ADDR'];
		$rl = new RateLimiter($name, $token, $ip);
		$rl->do_rate_limiting();
	}

	public function do_rate_limiting() {
		if ($this->is_request_exempt_from_rate_limiting())
			return;

		if ($this->has_user_exceeded_limit())
			$this->deny_request();

		$this->update_database();
	}

	private function has_user_exceeded_limit() {
		$usage = $this->fetch_usage_row();
		$limit = $this->get_rate_limit();

		if (!$usage)
			return false;

		if ($usage['count'] < $limit)
			return false;

		return true;
	}

	private function is_request_exempt_from_rate_limiting() {
		$query = "SELECT *
		          FROM auth_tokens
		          WHERE token='{$this->auth_token}'";
		$res = $this->db->query($query);
		$token = $this->db->fetch_assoc($res);

		return !$token['rate_limited'];
	}

	private function get_rate_limit() {
		global $config;

		$rateLimiting = @$config['rate_limiting'] ?: array();
		$explcitLimits = @$rateLimiting['explicit_limits'] ?: array();
		$moduleLimits = @$rateLimiting['module_limits'] ?: array();
		$defaultLimit = @$rateLimiting['default_limit'] ?: 10000;
		$limitModifiers = $this->limit_modifiers;
		$limitModifiers+= @$rateLimiting['limit_modifiers'] ?: array();
		$modifier = @$limitModifiers[$this->auth_token] ?: 1;

		if (isset($explcitLimits[$this->auth_token][$this->module])) {
			$rateLimit = $explcitLimits[$this->auth_token][$this->module];
		} elseif (array_key_exists($this->module, $moduleLimits)) {
			$rateLimit = $moduleLimits[$this->module] * $modifier;
		} else {
			$rateLimit = $defaultLimit * $modifier;
		}

		return (int) $rateLimit;
	}

	private function update_database() {
		$usage = $this->fetch_usage_row();

		if ($usage) {
			$new_count = $usage['count']+1;
			$query = "UPDATE api_usage
			          SET count={$new_count}
			          WHERE id={$usage['id']}";
			$res = $this->db->query($query);
		} else {
			$time = strftime("%Y-%m-%d %H:%M:%S");
			$query = "INSERT INTO api_usage (ip, module, created, count)
			          VALUES ('{$this->ip}', '{$this->module}', '$time', 1)";
			$res = $this->db->query($query);
		}
		return $res;

	}

	private function fetch_usage_row() {
		$query = "SELECT *
		          FROM api_usage
		          WHERE ip='{$this->ip}' AND
		                module='{$this->module}' AND
		                DATE_SUB(CURDATE(),INTERVAL 1 DAY) < created;";

		$res = $this->db->query($query);
		return $this->db->fetch_assoc($res);
	}

	private function deny_request() {
		header('HTTP/1.0 403 Forbidden');
		throw new RequestException('Too many requests today. You have been rate limited');
	}
}
