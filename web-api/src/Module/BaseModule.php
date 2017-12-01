<?php

namespace Daytalytics\Module;

use Daytalytics\Database;
use Daytalytics\ModuleRegistry;
use Daytalytics\ProxyServer;
use Daytalytics\UrlGetter;
use Daytalytics\DataGetter;
use Daytalytics\RequestException;
use Exception;
use Daytalytics\InputFormatter;
use Daytalytics\InputValidator;

/**
 * @name BaseModule
 * @created 2009-06-19
 * @description A base class from which additional modules may extend.
 */
abstract class BaseModule {
	   
    /**
     * 
     * @var array
     */
	public $errors = [];
	
	/**
	 *
	 * @var array
	 */
	public $data_sources = [];
	
	/**
	 *
	 * @var boolean
	 */
	public $show_results = true;
	
	/**
	 *
	 * @var array
	 */
	protected $proxies;
	
	/**
	 * 
	 * @var boolean
	 */
	protected $useProxies = true;
	
	/**
	 *
	 * @var array
	 */
	protected $proxyBlacklist = [];
	
	/**
	 * 
	 * @var Database
	 */
	public $db = null;
	
	/**
	 * Prevent modules from being loaded as service endpoints
	 * 
	 * @var string
	 */
	public static $private = false;
	
	abstract public function handle_request(array $params = []);
	
	abstract public function define_service();
	
	/**
	 * 
	 * @param Database $db
	 */
	final public function __construct(Database $db) {
		$this->db = $db;
        $this->construct();
	}
	
	final public function __destruct() {
	   $this->destruct();
	}
	
	public function construct(){}
	
	public function destruct(){}
	
	/**
	 * 
	 * @return string
	 */
	final public function name() {
	    $parts = explode('\\', get_class($this));
	    return end($parts);
	}
	
	/**
	 * 
	 * @param boolean $full
	 * @return string|array
	 */
	public function identify($full = false) {
	    if (!$full) {
	        return $this->name();
	    } else {
	        return [
	            'name' => $this->name(),
	            'input' => $this->define_service()
	        ];
	    } 
	}
	
	/**
	 * @name __handle_request()
	 * @description a precursor to the handle_request() method to allow
	 * the module to automatically do things, saving code in the specific
	 * module implementations.
	 *
	 * @name handle_request()
	 * @description if a BaseModule doesn't have a handle_request function, it 
	 * cannot be used externally. It will still be instantiated for internal use.
	 * @returns the appropriate results, as determined by the module
	 */
	final public function __invoke($params) {
	    $params = $this->format_params($params);
	    $valid = $this->validate_params($params);
	    if ($valid !== true) {
	        $message = 'Invalid/missing parameters: ' . implode(', ', array_keys($valid));
	        throw new RequestException($message);
	    }
		if (isset($params['do_cron_caching'])) {
			$result = $this->handle_cron_request($params);
		} else {
			$result = $this->handle_standard_request($params);
		}			
		return $result;
	}
	
	protected function format_params($params) {
	    $resource = 'default';
	    if (isset($params['type'])) {
	         $resource = $params['type'] = strtolower($params['type']);
	    }
	    $resources = $this->define_service();
	    if (isset($resources[$resource]) && !empty($resources[$resource]['parameters'])) {
	        foreach ($resources[$resource]['parameters'] as $param => $definition) {
	           if (isset($params[$param])) {
	               $params[$param] = InputFormatter::format($params[$param], $definition);
	           }
	        }
	    }
	    return $params;
	}
	
	protected function validate_params($params) {
	    $resource = 'default';
	    if (isset($params['type'])) {
	        $resource = $params['type'] = strtolower($params['type']);
	    }
	    $invalid = [];
	    $resources = $this->define_service();
	    if (isset($resources[$resource]) && !empty($resources[$resource]['parameters'])) {
	        foreach ($resources[$resource]['parameters'] as $param => $definition) {
	            $validatorResult = InputValidator::validate(@$params[$param], $definition);
	            if ($validatorResult !== true) {
	                $invalid[$param] = $validatorResult;
	            }
	        }
	    }
	    return $invalid ?: true;
	}

	protected function handle_standard_request($params) {
		if (!is_callable(array($this, 'handle_request'))) {
			$this->error('This module cannot handle a request');
			return false;
		}

		if(!isset($params['data_source'])) {
			$params['data_source'] = 'all';
		}
			
		$this->data_sources = array_fill_keys(array('local', 'live'), false);
		switch($params['data_source']) {
			case 'local' : {
				$params['data_source'] = 'local';
				$this->data_sources['local'] = true;
				break;
			}
			case 'live' : {
				$params['data_source'] = 'live';
				$this->data_sources['live'] = true;
				break;
			}
			default :
			case 'all' : {
				$params['data_source'] = 'all';
				$this->data_sources = array_fill_keys(array_keys($this->data_sources), true);
				break;
			}
		}

		$result = $this->handle_request($params);

		return $result;
	}

	protected function handle_cron_request($params) {
		//set 'live' as the only datasource
		$this->data_sources = array (
			'local' => false,
			'live' => true
		);
		$result = $this->do_cron_caching($params);
		return $result;
	}
	
	/**
	 * This just simulates how the web_information_api would call a module.
	 */
	final public function module_to_module_request($params) {
		$module = ModuleRegistry::get($params['module']);
        $results = $module->__invoke($params);		
		$errors = $module->get_errors();
		$this->get_errors();
		$this->errors = array_merge($this->errors, array_values($errors));
		if ($module->get_error()) {
		    $this->error($module->identify() . ': '. $module->get_error());
		}
		return $results;
	}
	
	/**
	 * Get contents from the internet
	 */
	function get($url, $post_vars = null, &$info=array(), $options=array(), $retry_options=array(), $proxy_options=array()) {
		$params = array(
			'url' => $url,
			'post_data' => $post_vars,
			'curl_options' => $options,
			'retry_options' => $retry_options,
            'proxy_request_options' => $proxy_options
		);
		$url_getter = new UrlGetter($this, $params);
		$data = $url_getter->get($info);
		return $data;
	}

	//Override this in module implementations.
	//
	//When get(...) is called, this method is invoked to give the calling
	//module a chance to specify whether get() should refetch the response.
	//
	//The idea being that if the response is something to the effect of "our
	//server is busy, try again soon" then this method can return true and 
	//get() will try again.
	public function should_retry_request($response, $retry_options = []) {
		return false;
	}

	//Modules that need to cache all their data regularly should override this
	public function do_cron_caching($params) {
		throw new RequestException("Not Implemented");
	}

	//Modules that allow importing data should override this method and have 
	//more specific checking.
	public function is_request_similar($request1, $request2) {
		return $request1 == $request2;
	}
	
	public function parse_query($query_string) {
		$query_parts = array();
		if (strpos($query_string, '?')===0) {
		    $query_string = substr($query_string, 1);
		}
		$matches = $groups = [];
		preg_match_all('/(?:^|&)([^=&]+)(?:=(.*?))?(?=&|$)/', $query_string, $matches, PREG_SET_ORDER);
		//for each key/value pair
		foreach($matches as $match) {
			//each key and value should have been url encoded before going into the query string
			$key = urldecode($match[1]);
			$value = urldecode($match[2]);

			//decode a url array
			$positon_of_first_opening_bracket = strpos($key, '[');
			$positon_of_last_opening_bracket = strpos($key, ']');
			//if it's a url-type array
			if ($positon_of_first_opening_bracket !== false && $positon_of_last_opening_bracket !== false) {

				//subsequent keys e.g. &data[model][field]=value keys: data,model,field
				preg_match_all('/\[([^\]]*)\]/', $key, $groups, PREG_PATTERN_ORDER);
				$keys = $groups[1];

				//first key
				$key = substr($key, 0, $positon_of_first_opening_bracket);

				//put the first url key part back in
				array_unshift($keys, $key);

				//start building the partial array, careful not to overwrite the
				//data another key/value pair has set
				$query_parts_offset =& $query_parts;
				for($i=0; $i<count($keys); $i++) {
					if ($keys[$i] != "") {
						if (!isset($query_parts_offset[$keys[$i]])) {
						    $query_parts_offset[$keys[$i]] = [];
						}	
						$query_parts_offset =& $query_parts_offset[$keys[$i]];
					} else {
						//set a default value for this new item (use array in case
						//the next itteration wants to add to it)
						$query_parts_offset[] = array();

						//find the array key of the last added item
						end($query_parts_offset);
						$last_added = each($query_parts_offset);
						$last_added_key = $last_added['key'];

						//advance our pointer to the newly created item
						$query_parts_offset =& $query_parts_offset[$last_added_key];
					}
				}
				//Not sure why this is here? perhaps just to cancel out the by-reference assignment?
				$query_parts_offset = $value;
			} else {
				$query_parts[$key] = $value;
			}
		}
		return $query_parts;
	}
	
	public function get_proxy() {
		if (!isset($this->proxies) && $this->useProxies) {
			$proxies = ProxyServer::get_proxies();
			foreach ($proxies as $key => $proxy) {
				if (!$this->is_proxy_blacklisted($proxy)) {
				    $this->proxies[] = $proxy;
				}	
			}
		}
		
		if (empty($this->proxies)) {
		    return ProxyServer::get_default_proxy();
		}
		
		$proxy = false;
		while ((!$proxy) && ($total = count($this->proxies))) {
		    $key = mt_rand(0, $total - 1);
            if (!$this->is_proxy_blacklisted($proxy)) {
                if (!ProxyServer::has_recent_errors($this->proxies[$key]['id'], $this->identify())) {
	                $proxy = $this->proxies[$key];
	            }
	        }
		    if (!$proxy) {
		        unset($this->proxies[$key]);
		        $this->proxies = array_values($this->proxies);
		    }
		}

		if (empty($proxy)) {
		    trigger_error($this->identify() . ' has no proxies', E_USER_WARNING);
		}
		
		return $proxy;
	}
		
	/**
	 * @name is_proxy_blacklisted()
	 * @desription test whether the given proxy is undesirable. E.g. they're not
	 * blocking our requests from too much use.
	 * This should be overriden in a module if required.
	 * This need not handle limits
	 */
	public function is_proxy_blacklisted($proxy, $set = false) {
		$is_blacklisted = false;
		//remove
		foreach($this->proxyBlacklist as $_key => $_proxy) {
			if (!empty($_proxy['id']) && !empty($proxy['id']) && $_proxy['id'] == $proxy['id']) {
				if (func_num_args() > 1 && !$set) {
					unset($this->proxyBlacklist[$_key]);
				} else {
					$is_blacklisted = true;
				}
				break;
			}
		}
		if ($set) {
			$this->proxyBlacklist[] = $proxy;
			$is_blacklisted = true;
		}
		return $is_blacklisted;
	}
	
	/**
	 * @name is_valid_response()
	 * @description test whether the response is expected
	 */
	public function is_valid_response($response) {
		return $response !== false;
	}
	
	
	/**
	 * The generic message from response basically intended for auth tokens with can_view_errors = 1 
	 * 
	 * @param string $response
	 * @return string
	 */
	public function getErrorFromResponse($response) {
		return str_replace(array("\r", "\n"), " ", strip_tags($response));
	}
	
	
	/**
	 * get_redirect_url()
	 * Gets the address that the provided URL redirects to,
	 * or FALSE if there's no redirect. 
	 *
	 * @param string $url
	 * @return string
	 */
	public function get_redirect_url($url){
		$url_parts = @parse_url($url);
		if (!$url_parts) return false;
		if (!isset($url_parts['host'])) return false; //can't process relative URLs
		if (!isset($url_parts['path'])) $url_parts['path'] = '/';

		$sock = fsockopen($url_parts['host'], (isset($url_parts['port']) ? (int)$url_parts['port'] : 80), $errno, $errstr, 30);
		if (!$sock) return false;

		$request = "HEAD " . $url_parts['path'] . (isset($url_parts['query']) ? '?'.$url_parts['query'] : '') . " HTTP/1.1\r\n";
		$request .= 'Host: ' . $url_parts['host'] . "\r\n";
		$request .= "Connection: Close\r\n\r\n";
		fwrite($sock, $request);
		$response = '';
		while(!feof($sock)) $response .= fread($sock, 8192);
		fclose($sock);

		if (preg_match('/^Location: (.+?)$/m', $response, $matches)){
			return trim($matches[1]);
		} else {
			return false;
		}

	}
	
	/**
	 * Returns a complete table name, with prefix, and mysql escaped
	 */
	final public function table_name_sql_safe($table_name) {
		if (!isset($this->table_name_prefix)) {
			throw new Exception("No table name prefix is set.");
		}
		
		return $this->db->escape_string($this->table_name_prefix . $table_name);
	}
	
	public function get_final_datasource() {
		return DataGetter::get_final_datasource();
	} 
	
	/**
	 * @name get_errors()
	 * @description just a wrapper for get_error(true) to return all errors
	 */
	final public function get_errors() {
	    return $this->errors;
	}
	
	/**
	 * @name get_error()
	 * @usage
	 * get_error(): gets the description of the newest error.
	 * get_error(true): gets the description and level of all errors.
	 */
	final public function get_error($full=false) {
	    if (empty($this->errors)) {
	        return false;
	    }
	    $error = end($this->errors);
	    if ($full) {
	        return $error;
	    } else {
	        return $error['description'];
	    }
	}
	
	/**
	 * @name get_error_level()
	 * @usage get_error_level()
	 * @description returns the error level of the newest error.
	 */
	final public function get_error_level() {
	    $error = $this->get_error(true);
	    if (empty($error)) {
	        return 0;
	    } else {
	        return $error['level'];
	    }
	}
	
	final protected function error($description, $level = E_USER_NOTICE) {
	    trigger_error($description, $level);
	    $this->errors[] = array(
	        'description' => $description,
	        'level' => $level
	    );
	}
	
	//Returns an array of datasources in the order that a DataGetter should try.
	//
	//An array of datasources to try can be passed in and, if the data sources
	//are enabled, they will be tried in the order given.
	final protected function format_data_sources_for_data_getter(array $order = ["local", "live"]) {
	    $sources_to_check = array();
	    foreach ($order as $source) {
	        if ($this->data_sources[$source]) {
	            $sources_to_check[] = $source;
	        }
	    }
	    return $sources_to_check;
	}
}