<?php

namespace Daytalytics;

use Exception;

class MoneyConverterParser {
	private $data;

	function __construct($data) {
		$this->data = $data;
	}

	//Make sure all the output currencies are available in the data.
	//
	//If a currency doesnt exist, throw and exception.
	function validate_output_currencies($currencies) {
		foreach ($currencies as $currency){
			if (!in_array($currency, $this->get_currencies())) {
				throw new RequestException('Invalid output currency: '. $currency);
			}
		}
	}

	//get an array of all currencies represented in the data
	//e.g. ["USD", "NZD", "AUD", ...]
	function get_currencies() {
		if (!isset($this->currencies)) {
			$this->currencies = array();
			foreach ($this->get_all_rates() as $rate){
				$this->currencies[] = $rate['currency'];
			}
		}
		return $this->currencies;
	}

	//grab a subset of all the rates in the data
	function get_rates($target_rates) {
		$this->validate_output_currencies($target_rates);
		$matching_rates = array();
		foreach ($this->get_all_rates() as $rate){
			if (in_array($rate['currency'], $target_rates)) {
				$matching_rates[] = $rate;
			}
		}

		return $matching_rates;
	}

	//extract all rates from the data
	function get_all_rates() {
		if (!isset($this->all_rates)) {
			$xml = new \SimpleXMLElement($this->data);
			foreach ($xml->channel->item as $item){
				$this->all_rates[] = $this->parse_item($item);
			}
		}
		return $this->all_rates;
	}

	function parse_item($item) {
		if(preg_match('#^(.*)/.*$#', $item->title, $matches) == 0){
			throw new Exception("Malformed input data");
		}
		$currency = $matches[1];

		if(preg_match('#^.*=\s*([0-9.]*)\s*.*$#', $item->description, $matches) == 0){
			throw new Exception("Malformed input data");
		}
		$rate = $matches[1];
		
		return array(
			'currency' => $currency,
			'rate' => $rate
		);
	}
}
