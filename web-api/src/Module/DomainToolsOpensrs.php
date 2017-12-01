<?php

namespace Daytalytics\Module;

define('OPENSRS_USER', 'doubledotmedia');
define('OPENSRS_KEY', '');
define('OPENSRS_ENV', 'live');

require_once(getenv('DDM_COMMON') . 'classes/opensrs/opensrs_api.php'); // server

use Daytalytics\RequestException;
use OpensrsApi;

class DomainToolsOpensrs extends BaseModule {

    protected $useProxies = false;
    
	public function define_service() {
		return [
            'check' => [
                'parameters' => [
                    'domain' => [
                        'description' => 'Domain to check, exclude tld',
                        'required' => true
                     ],
                    'tld' => [
                        'type' => 'array',
                        'collectionFormat' => 'multi',
                        'description' => 'tld to check. true means taken.',
                        'maxItems' => 50,
                        'minItems' => 1,
                        'items' => [
                            'type' => 'string'
                        ],
                        'required' => true
                     ],
                ]
		    ]
		];
	}

	public function handle_request(array $params = []) {
		if (empty($params['type'])) {
			throw new RequestException('Invalid type');
		}

		switch ($params['type']) {
			case 'check':
				if (empty($params['domain'])) {
					throw new RequestException('Invalid domain passed');
				}
				$domain = $params['domain'];
				$domain = str_replace('www.', '', strtolower($domain));
				$domain = explode('/', $domain);
				$domain = array_shift($domain);
				if ($domain == '') {
					throw new RequestException('Invalid domain passed');
				}
				if ( (strpos($domain, '.')) || (strpos($domain, '_')) ) {
					throw new RequestException('Invalid domain passed');
				}
				if (!isset($params['tld']) || empty($params['tld'])) {
					throw new RequestException('Invalid tlds passed');
				}
				$tlds = array_filter((array) $params['tld']);
				if (count($tlds) == 0) {
					throw new RequestException('No tlds passed. Min 1!');
				}
				if (count($tlds) > 50) {
					throw new RequestException('To many tlds. Max 50!');
				}

				return $this->get_domain_check_opensrs($domain, $tlds);
				break;
			default:
				throw new RequestException("Invalid type");
				break;
		}
	}

	function get_domain_check_opensrs($domain, $tlds) {
		$query =  array(
			'domain' => $domain,
			'selected' => implode (';', $tlds) //semicolon separated list of tlds to check
		);

		if (strpos($domain, '-') == false){
			$cldomain = '';
		}else{
			$cldomain = str_replace('-', '', $domain);
		};

		$lookup = OpensrsApi::lookupDomain($query);

		if ($lookup && !empty($lookup->resultFullRaw['is_success'])) {
			$results= array();

			foreach ($lookup->resultRaw as $result) {
				if ($result['status'] =='available') {
					$istaken = 'false';
				} else {
					$istaken = 'true';
				}

				$tld = str_replace($domain . '.', '',  $result['domain']);
				if (($tld == $result['domain']) && ($cldomain != '')){
					$tld = str_replace($cldomain . '.', '',  $result['domain']);
				}

				$results[] = array('domain' => $result['domain'], 'tld' => $tld, 'result' => $istaken);
			}

		}else{
			throw new RequestException("OpenSRS error");
		}

		return $results;
	}
}
