<?php
namespace Daytalytics\Tests\Unit\TestCase\Module;

use Daytalytics\Module\CurrencySource;
use Daytalytics\Tests\Lib\DbFixtureTestCase;

/**
 *
 * @coversDefaultClass Daytalytics\Module\CurrencySource
 *
 */
class CurrencySourceTest extends DbFixtureTestCase {
	
    /**
     * @var CurrencySource CurrencySource
     */
	public $module;

	protected function setUp() {
		parent::setUp();
		$this->module = new CurrencySource($this->db);
	}
	
	public function testIdentity() {
		$this->assertRegExp('/^CurrencySource$/i', $this->module->identify() , "Module identifier is correct.");
	}

	public function testHandleRequestReturnsTheProperFormat() {
		$results = $this->module->handle_request( array(
			'from' => 'NZD',
			'to' => array('USD', 'AUD'),
		    'type' => 'convert'
		));
			
		$this->assertTrue(array_key_exists('currency', $results[0]));
		$this->assertTrue(array_key_exists('rate', $results[0]));

		$this->assertTrue(array_key_exists('currency', $results[1]));
		$this->assertTrue(array_key_exists('rate', $results[1]));
	}

	public function testFullIdentityIsProperlyFormatted() {
		$fullId = $this->module->identify(true);
		$this->assertTrue(is_array($fullId));
		$this->assertTrue(array_key_exists('name', $fullId));
		$this->assertTrue(array_key_exists('input', $fullId));
	}

	public function testSuccessfulRequest() {
		$request = array('from' => 'NZD', 'to' => array('AUD'), 'type' => 'convert');
		$response = $this->module->handle_request($request);
		$this->assertTrue(is_array($response));
	}

	public function testExceptionIsThrownWhenNoInputCurrencySpecified() {
		$request = array('to' => array('NZD'), 'type' => 'convert');

		$this->expectException('Daytalytics\RequestException');
		$this->module->handle_request($request);
	}

	public function testExceptionIsThrownWhenNoOutputCurrenciesSpecified() {
		$request = array('from' => 'NZD', 'type' => 'convert');

		$this->expectException('Daytalytics\RequestException');
		$this->module->handle_request($request);
	}
}