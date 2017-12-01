<?php
namespace Daytalytics\Tests\Unit\TestCase\Module;

use Daytalytics\Module\TradeShow;
use Daytalytics\Tests\Lib\DbFixtureTestCase;
use \ReflectionClass;

/**
 *
 * @coversDefaultClass Daytalytics\Module\TradeShow
 *
 */
class TradeShowTest extends DbFixtureTestCase {
	
    /**
     * @var TradeShow TradeShow;
     */
	public $module;

	protected function setUp() {
		parent::setUp();
		$this->module = new TradeShow($this->db);
		$reflectionClass = new ReflectionClass($this->module);
		$useProxies = $reflectionClass->getProperty('useProxies');
		$useProxies->setAccessible(true);
		$useProxies->setValue($this->module, false);
		$this->module->data_sources = array('live' => true, 'local' => false);
	}
	
	public function testIdentity() {
		$this->assertRegExp('/^TradeShow$/i', $this->module->identify() , "Module identifier is correct.");
	}
	
	public function testCountries() {
		$request = array('module' => 'TradeShow', 'type' => 'Countries');
		$results = $this->module->module_to_module_request($request);
		
		$this->assertTrue(is_Array($results));
		$this->assertTrue(array_key_exists('philippines', $results));
		$this->assertTrue(array_key_exists('usa', $results));
		$this->assertTrue(array_key_exists('new-zealand', $results));
	}
	
	public function testEventsSummaryByCountry() {
		$request = array('module' => 'TradeShow', 'type' => 'EventsSummaryByCountry', 'countryCode' => 'US');
		$results = $this->module->module_to_module_request($request);
		$this->assertTrue(is_Array($results));
		$this->assertTrue(array_key_exists('type', $results));
		$this->assertTrue(array_key_exists('industry', $results));
		$this->assertTrue(array_key_exists('country', $results));
		$this->assertTrue(array_key_exists('city', $results));
		
		$this->assertTrue(!empty($results['type']));
		$this->assertTrue(!empty($results['industry']));
	}
	
	public function testEventsSummaryByCountryNoCountryCodeParam() {
		$request = array('module' => 'TradeShow', 'type' => 'EventsSummaryByCountry');
		$this->expectException('Daytalytics\RequestException', "A Daytalytics\RequestException is thrown when countryCode param is empty.");
		$this->module->module_to_module_request($request);
	}
	
	public function testEventListByCountryAndCity() {
		$request = array('module' => 'TradeShow', 'type' => 'EventListByCountryAndCity', 'countryCode' => 'US', 'city' => 'albany');
		$results = $this->module->module_to_module_request($request);
		$this->assertTrue(is_Array($results));
		
		$result = current($results);
		$this->assertTrue(is_Array($result));
		$this->assertTrue(array_key_exists('title', $result));
		$this->assertTrue(array_key_exists('guid', $result));
		$this->assertTrue(array_key_exists('date_start', $result));
		$this->assertTrue(array_key_exists('date_end', $result));
	}
	
	public function testEventListByCountryAndCityInvalidCountryCodeAndCityParams() {
		$request = array('module' => 'TradeShow', 'type' => 'EventListByCountryAndCity', 'countryCode' => 'NOT A COUNTRY CODE', 'city' => 'NOT A CITY');
		$this->expectException('Daytalytics\RequestException', "A Daytalytics\RequestException is thrown when countryCode or city params is invalid.");
		$this->module->module_to_module_request($request);
	}
	
	public function testEventListByCountryAndCityNoCountryCodeAndCityParams() {
		$request = array('module' => 'TradeShow', 'type' => 'EventListByCountryAndCity');
		$this->expectException('Daytalytics\RequestException', "A Daytalytics\RequestException is thrown when countryCode and city params are empty.");
		$this->module->module_to_module_request($request);
	}		
}