<?php

namespace Daytalytics\Module;

use Daytalytics\DataGetter;
use Daytalytics\RequestException;

/**
 * BBB server module
 * 
 * @see https://developer.bbb.org
 */
class Bbb extends BaseModule {

    protected $useProxies = false;
    
	/**
	 * Token will expire Apr 7, 2019 5:10:22 AM.
	 */
	protected $token = 'FXl1m6NMFTYQf2xrF8slsxofEy9_XCTtvSL9WJltbTVr5XAAsD50k-4qjd-9XOUqYZDz52GNrzzjms7VdRCZHhHHO3ih_1mlEbpnqWEew5TOXEtONw8aauT5rvdWijnplNTJ6O07UuUt3sL9WgtNJ3g398uMb_XfNCkanpBKXsTa2HYJdujHyS3Fd2oVYfSaIFWMc0PXdN8YE9whLlhIbgNYi7Ffobsyit6x_HoK9Pay7TekKvu_HQyH6wZiLqkMQ2wW97AwXm3CCfWNKRJkqHGs0sAaN3yjrYFUTzbcqGpSMcwwzJqrPCvSqJy6XE6J9GrMAGPH7FVnMmPCUn4JCJy4vfv6-kmfGdxgDeirJWWAqw5FPLR4tfRabmLyWsmTS34LmL3x_H7v210TmybfG7HbfH3vvw7EXx36Fx8mbdxsb9Dl0CaBsZISmrQqOTLjPbNXj3lVyOEYHLOCOyN6lepLsfuFu17HF-IDWJj-B5AJLNHv51APKB5-Ix_LS6_37RizMJnYoFZetIBIFn78bgoOvjEJSIgRQyMeBtbnCzAChMK4buhmC_qF7j1jtfoFL9bGTO3ajRu3LJHpDuJBISQRaJu9uoSJ3zewtqoaEmBNrSvy8sPhcMfsIWf2D-MO-y1YDwEQ_3keCoiTQ02evTFgTKA';
	
	protected $endpoint = 'https://api.bbb.org/api/orgs/search';
	
	public function define_service() {
	   return [
	       'organizationalsearchbydomain' => [
	           'parameters' => [
	               'domain' => [
	                   'description' => 'Domain name e.g. example-domain.com',
	                   'required' => true
	               ]
	           ]
	       ]
	   ];
	}
	
	/**
	 * 
	 * @param unknown $params
	 * @throws RequestException
	 * @return multitype:
	 */
	public function handle_request(array $params = []) {
		switch (@$params['type']) {
			case 'organizationalsearchbydomain':
				$result = $this->searchOrginizationByDomain(@$params['domain']);
			break;
			default:
				throw new RequestException('A valid type is required');
			break;
		}
		return $result;
	}
	
	
	public function searchOrginizationByDomain($domain) {
		if(empty($domain)) {
			throw new RequestException('Domain is required');
		}
		
		$requestUrl = "{$this->endpoint}?BusinessURL={$domain}&PageSize=1";
		$result = $this->request($requestUrl);
		
		if($result) {
			$result = json_decode($result, true);
			if(!empty($result['TotalResults']) && !empty($result['SearchResults'])) {
				// Return only the first search
				return current($result['SearchResults']);
			}
		}
		throw new RequestException('Data not available.');
	}
	
	
	protected function request($requestUrl) {
		$cacheKey = sha1($requestUrl);
		$dataGetterParams = array(
			'cache_key' => $cacheKey,
			'data_sources' => $this->format_data_sources_for_data_getter(),
			'url_getter_params' => array(
				'url' => $requestUrl,
				'curl_options' => array(
					CURLOPT_HTTPHEADER => array(
						"Accept: application/json",
						"Authorization: Bearer {$this->token}"
					)
				)
			)
		);
		
		return DataGetter::get_data_or_throw($this, $dataGetterParams);
	}	
}