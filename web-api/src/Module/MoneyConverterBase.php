<?php

namespace Daytalytics\Module;

use Daytalytics\DataGetter;
use Daytalytics\Mailer;
use Daytalytics\RequestException;

abstract class MoneyConverterBase extends BaseModule {

	protected $baseCurrency = 'USD';	
	
	public function define_service() {
	    return [
	        'convert' => [
	            'parameters' => [
	                'from' => [
	                    'description' => 'A 3 letter currency code eg NZD. [Options are available from currencies]',
	                    'required' => true
	                ],
	                'to' => [
	                    'type' => 'array',
	                    'collectionFormat' => 'multi',
	                    'description' => 'An array of 3 letter currency codes to convert to. [Options are available from currencies]',
	                    'required' => true,
	                    'items' => [
	                        'type' => 'string'
	                    ]
	                ]
	            ]
	        ],
	        'currencies' => []
	    ];
	}
	
	public function handle_request(array $params = []) {
		if(!isset($params['type'])) {
			throw new RequestException('Type must be specified.');
		}
	
		switch ($params['type']) {
			case 'convert':
				if(empty($params['from'])) {
					throw new RequestException('A currency to convert from must be specified.');
				}
	            $to = array_filter((array) @$params['to'] ?: []);
				if(empty($to)) {
					throw new RequestException('One or more currencies to convert to must be specified.');
				}
				return $this->get_conversion_data($params['from'], $to);
				break;
			case 'currencies':
				return $this->get_all_currency_codes();
				break;
			default:
				throw new RequestException("Invalid type.");
				break;
		}
	}
	
	
	abstract public function get_conversion_data($baseCurrency, array $toCurrencies);
	
	abstract public function get_all_currency_codes();
	
	abstract protected function url_for_currency($currency);
	
	public function do_cron_caching($params) {
		$currency_codes = $this->get_all_currency_codes();
		foreach ($currency_codes as $code){
			try {
				$this->get_data($code);
			} catch (Exception $e) {
				Mailer::mail_admins("Moneyconverter cron", "Failed while caching $code: " . $e->getTracesaAsString());
				continue;
			}
		}
	}	
	
	protected function get_data($currency) {
		$url = $this->url_for_currency($currency);
		$dg_params = array(
			'cache_key' => $currency,
			'cache_expiry_time' => '-1 hour',
			'data_sources' => $this->format_data_sources_for_data_getter(array("local", "live")),
			'url_getter_params' => array(
				'url' => $url
			),
		);
		return DataGetter::get_data_or_throw($this, $dg_params);
	}
	
	
}
