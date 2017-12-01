<?php

namespace Daytalytics\Module;

use Daytalytics\RequestException;

class Search extends BaseModule {
 
     public function define_service() {
        $params = [
            'engines' => [
                'description' => 'Search engine to query',
                'type' => 'array',
                'collectionFormat' => 'csv',
                'options' => ['google', 'bing'],
                'items' => [
                    'type' => 'string'
                ]
            ],
            'keyword' => [
                'description' => 'Keyword search term',
                'required' => true
            ],
            'limit' => [
                'description' => 'Maximum number of search results',
                'default' => 10,
                'minimum' => 1,
                'maximum' => 200
            ],
        ];
        return [
            'ns' => [
                'parameters' => $params
            ],
            'pl' => [
                'parameters' => $params
            ],
            'ps' => [
                'parameters' => $params
            ]
        ];
    }

	public function handle_request(array $params = []) {
		$params['type'] = @$params['type'] ? strtoupper($params['type']) : 'NS';
		$params['limit'] = @$params['limit'] ?: '10';
		$engines = @$params['engines'] ?: [];
		if (!is_array($engines)) {
			$engines = explode(',', $engines);
		}
		$engines = array_filter($engines);
		if (empty($engines)) {
		    throw new RequestException('One or more search engines must be specified.');
		}
		$engines = array_map('strtolower', $engines);
		if (array_diff($engines, ['bing', 'google'])) {
		    throw new RequestException('One or more search engines are invalid.');
		}
		$params['engines'] = $engines;
		if (!isset($params['keyword']) || $params['keyword'] === '') {
			throw new RequestException('A keyword must be specified.');
		}
		return $this->search($params);
	}

	protected function search($params) {
		$engines = array_unique($params['engines']);
		$results = array();
		foreach ($engines as $engine) {
			$engine = strtolower($engine);
			$engineKey = ucfirst($engine);
			$request = $params;
			$request['module'] = $engineKey . 'Search';
			$result = $this->module_to_module_request($request);
			if ($result !== false) {
			    $results[$engineKey] = $result;
			}
		}
		return $results;
	}
}