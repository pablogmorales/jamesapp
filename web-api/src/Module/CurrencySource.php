<?php

namespace Daytalytics\Module;

class CurrencySource extends MoneyConverterBase {

	protected $currencyModule = 'MoneyConverterFixerIo';
	
	public function get_conversion_data($baseCurrency, array $toCurrencies) {
	    $params = [
	        'from' => $baseCurrency,
	        'to' => $toCurrencies,
	        'module' => $this->currencyModule,
	        'type' => 'convert'
	    ];
	    return $this->module_to_module_request($params);
	}
	
	public function get_all_currency_codes() {
	    $params = [
	        'module' => $this->currencyModule,
	        'type' => 'currencies'
	    ];
	    return $this->module_to_module_request($params);
	}
	
	protected function url_for_currency($currency) {}
}
