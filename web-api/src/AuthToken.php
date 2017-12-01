<?php

namespace Daytalytics;

use Daytalytics\Module\BaseModule;
use Zend\Diactoros\ServerRequest;

/**
 * @property $id integer
 * @property $token string
 * @property $secret string
 * @property $enabled integer
 * @property $description string
 * @property $can_view_errors integer
 * @property $can_import_data integer
 * @property $rate_limited integer
 */
class AuthToken {
	
    /**
     * @var Database Database
     */
    private $db;

    /**
     * @var ServerRequest ServerRequest
     */
    private $request;
	
    /**
     * @var array
     */
	private $data = [];
	
	/**
	 * @var AuthToken AuthToken
	 */
	protected static $authToken;

	/**
	 * @var string
	 */
	const table_name = 'auth_tokens';

	/**
	 * Obtain auth token from request
	 *
	 * @param ServerRequest $request
	 * @return \Daytalytics\AuthToken
	 */
	public static function fromRequest(ServerRequest $request) {
	    $authToken = new AuthToken($request);
	    $authToken->validateRequestToken();
	    static::$authToken = $authToken;
	    return static::$authToken;
	}
	
    /**
	* Obtain auth token from GET request (cron)
	*/
	public static function fromRequestQuery(ServerRequest $request) {
	    $authToken = new AuthToken($request);
	    $authToken->validateQueryRequestToken();
	    static::$authToken = $authToken;
	    return static::$authToken;
	}
	
    /**
     * @return \Daytalytics\AuthToken
     */
	public static function findRromRequest() {
	    return static::$authToken;
	}
	
	/**
	 * @param ServerRequest $request
	 */
	public function __construct(ServerRequest $request) {
		$this->db = WebInformationApi::get_instance()->db;
		$this->request = $request;
	}
	
	public function __get($property) {
	    if (isset($this->data[$property])) {
	        if ($property === 'can_view_errors') {
	            return (@$this->data['can_view_errors'] || getenv('APP_ENVIRONMENT') == 'development');
	        }
	        return $this->data[$property];
	    }
	}

	public function set_can_view_errors($can_view_errors) {
		$this->data['can_view_errors'] = (bool) $can_view_errors;
	}
	
	public function validateRequestSignature() {
	    $signatureHeader = $this->request->getHeader('Api-Auth-Signature');
	    if (empty($signatureHeader)) {
	        throw new RequestException('Invalid signature', 400);
	    }
	    $signature = reset($signatureHeader);
	    $params = $this->request->getQueryParams();
	    $requestSignature = $this->calculateRequestSignature($params); 
	    if ($requestSignature != $signature) {
	        //try alternate encoding
	        $requestSignature = $this->calculateRequestSignature($params, PHP_QUERY_RFC3986);
	        if ($requestSignature != $signature) {
	            throw new RequestException('Invalid signature', 400);
	        }
	    }
	}
	
	public function calculateRequestSignature(array $params, $encoded = null) {
	    $path = @$params['module'] ?: '';
	    if (isset($params['type'])) {
	        $path .= '/' . $params['type'];
	    }
	    unset($params['module'], $params['type']);
	    if(!empty($params)) {
	        if (is_int($encoded)) {
	            $path .= '?' . http_build_query($params, null, '&', $encoded);
	        } else {
	            $path .= '?' . http_build_query($params);
	        }
	    }

	    $requestSignature = hash_hmac('sha1', $path, $this->secret);	    
	    return $requestSignature;
	}

	public function validateRequestToken() {		
	    $tokenHeader = $this->request->getHeader('Api-Auth-Token');
		if (empty($tokenHeader)) {
			throw new UnauthorizedRequestException("Auth token required.", 401);
		}
		$token = reset($tokenHeader);
		$this->obtainRequestToken($token);
    }
		
	public function validateQueryRequestToken() {
	    $params = $this->request->getQueryParams();
	    $token = @$params['auth_token']?:'';
	    if (empty($token)) {
	        throw new UnauthorizedRequestException("Auth token required.", 401);
	    }
	    $this->obtainRequestToken($token);
	}
	
	public function trackUsage(BaseModule $module, array $params = []) {
	    $token_id = (int) $this->id;
	    $identifier = $this->db->escape_string($module->identify());
	    $type = @$params['type'] ? $this->db->escape_string(strtolower($params['type'])) : '';
	    unset($params['module'], $params['type'], $params['time'], $params['auth_token'], $params['_']);
	    $other = $this->db->escape_string(serialize($params));
	    $created = date('Y-m-d H:i:s');
	    $this->db->unbuffered_query("INSERT DELAYED INTO auth_token_usage SET created='{$created}', token_id={$token_id}, module='{$identifier}', type='{$type}', other='{$other}'");
	}
		
    protected function obtainRequestToken($token) {
		$res = $this->db->query('SELECT * FROM '. self::table_name .' WHERE token="'.$this->db->escape_string($token).'" AND enabled="1" LIMIT 1');
		$fields = $this->db->fetch_assoc($res);
		if ($fields === false) {
			throw new UnauthorizedRequestException("Invalid auth token.", 403);
		}
		$this->data = $fields;
	}
	
	/**
	 *
	 * @deprecated use method findRromRequest()
	 */
	public static function find_from_request() {
	    return static::findRromRequest();
	}
	
	/**
	 * @deprecated use $can_view_errors property
	 */
	public function can_view_errors() {
	    return $this->can_view_errors;
	}
}
