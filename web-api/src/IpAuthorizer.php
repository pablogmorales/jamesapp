<?php

namespace Daytalytics;

use Zend\Diactoros\ServerRequest;

class IpAuthorizer {

    /**
     * @var ServerRequest ServerRequest
     */
    protected $request;
    
    /**
     * @var array
     */
    protected $config;
    
    /**
     * 
     * @param ServerRequest $request
     */
	public static function fromRequest(ServerRequest $request) {
		$auth = new IpAuthorizer($request);
		$auth->authorize();
	}
	
	/**
	 * 
	 * @param ServerRequest $request
	 */
	public function __construct(ServerRequest $request) {
	    global $config;
	    $this->config = $config['ip_auth'];
	    $this->request = $request;   
	}

	/**
	 * 
	 * @throws UnauthorizedRequestException
	 */
	public function authorize() {
		if (!$this->is_authorized()) {
			throw new UnauthorizedRequestException('Forbidden', 403);
		}
	}

	/**
	 * 
	 * @return boolean
	 */
	protected function is_authorized() {
		if (!$this->should_check()) {
			return true;
		}
		$server = $this->request->getServerParams();
		return in_array(@$server['REMOTE_ADDR'], $this->config['allowed_ips']);
	}

	/**
	 * 
	 * @return boolean
	 */
	protected function should_check() {
		if (getenv('APP_ENVIRONMENT') != "production") {
			return false;
		}
		$server = $this->request->getServerParams();
		return in_array(basename(@$server['PHP_SELF'] ?: ''), $this->config['scripts_to_check']);
	}
}