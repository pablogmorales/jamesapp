<?php
namespace Daytalytics\Tests\Unit\TestCase\Module;

use Daytalytics\Module\MoneyConverterFixerIo;
use Daytalytics\Tests\Lib\DbFixtureTestCase;

/**
 *
 * @coversDefaultClass Daytalytics\Module\MoneyConverterFixerIo
 *
 */
class MoneyConverterFixerIoTest extends DbFixtureTestCase {
	
    /**
     * @var MoneyConverterFixerIo MoneyConverterFixerIo;
     */
	public $module;

	protected function setUp() {
		parent::setUp();
		$this->module = new MoneyConverterFixerIo($this->db);
		$this->module->data_sources = array('live' => true, 'local' => false);
	}
	
	public function testIdentity() {
		$this->assertRegExp('/^MoneyConverterFixerIo$/i', $this->module->identify() , "Module identifier is correct.");
	}
	
	public function testGetConversionData() {
		$request = array('module' => 'MoneyConverterFixerIO', 'type' => 'convert', 'from' => 'USD', 'to' => array('PHP', 'NZD'));
		$results = $this->module->module_to_module_request($request);
		
		// PHP
		$this->assertTrue(array_key_exists('currency', $results[0])); 
		$this->assertTrue($results[0]['currency'] == 'PHP');
		$this->assertTrue(array_key_exists('rate', $results[0]));
		
		// NZD
		$this->assertTrue(array_key_exists('currency', $results[1]));
		$this->assertTrue($results[1]['currency'] == 'NZD');
		$this->assertTrue(array_key_exists('rate', $results[1]));
	}

	public function testGetAllCurrencyCodes() {
		$request = array('module' => 'MoneyConverterFixerIO', 'type' => 'currencies');
		$results = $this->module->module_to_module_request($request);
		
		// Check some
		$this->assertTrue(in_array('PHP', $results));
		$this->assertTrue(in_array('EUR', $results));
		$this->assertTrue(in_array('NZD', $results));
	}
}