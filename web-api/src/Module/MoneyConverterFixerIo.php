<?php

namespace Daytalytics\Module;

class MoneyConverterFixerIo extends MoneyConverterBase {
	
    protected $useProxies = false;
    
	protected $baseApiUrl = 'http://api.fixer.io/latest';
	
	public static $private = true;
	
	public function get_conversion_data($baseCurrency, array $toCurrencies) {
		$rates = array();
		$currencies = array();
		$resultAsJson = $this->get_data($baseCurrency);
		if(!empty($resultAsJson)) {
			$currencies = $this->resultAsCurrencyCodeAndRates($resultAsJson);
		}
		
		foreach ($toCurrencies as $currency) {
			if(!empty($currencies[$currency])) {
				$rates[] = array(
					'currency' => $currency,
					'rate' => $currencies[$currency]
				);
			}
		}
		return $rates;
	}
	
	public function get_all_currency_codes() {	
		$currencies = array();
		$resultAsJson = $this->get_data($this->baseCurrency);
		if(!empty($resultAsJson)) {
			$currencies = $this->resultAsCurrencyCodes($resultAsJson);
		}
		return $currencies;
	}
	
	protected function url_for_currency($currency) {
		return sprintf('%s?base=%s', $this->baseApiUrl, $currency);	
	}

	protected function resultAsCurrencyCodes($json) {
		$currencies = array();
		$result = json_decode($json, true);
		if(!empty($result['rates'])) {
			foreach($result['rates'] as $currency => $rate) {
				$currencies[] = $currency;
			}
		}
		asort($currencies);
		
		return $currencies;
	}
	
	protected function resultAsCurrencyCodeAndRates($json) {
		$currencies = array();
		$result = json_decode($json, true);
		if(!empty($result['rates'])) {
			foreach($result['rates'] as $currency => $rate) {
				$currencies[$currency] = $rate;
			}
		}
		return $currencies;
	}
}
