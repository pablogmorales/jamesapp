<?php
namespace Daytalytics\Tests\Unit\TestCase\Module;

use Daytalytics\Module\NortonWebsafe;
use Daytalytics\Tests\Lib\DbFixtureTestCase;

/**
 *
 * @coversDefaultClass Daytalytics\Module\NortonWebsafe
 *
 */
class NortonWebsafeTest extends DbFixtureTestCase {
	
    /**
     * @var NortonWebsafe NortonWebsafe;
     */
	public $module;

	protected function setUp() {
		parent::setUp();
		$this->module = new NortonWebsafe($this->db);
		$this->module->data_sources = array('live' => true, 'local' => false);
	}
	
	public function testIdentity() {
		$this->assertRegExp('/^NortonWebsafe$/i', $this->module->identify() , "Module identifier is correct.");
	}
	
	public function testInvalidType() {
		$request = array('module' => 'NortonWebsafe', 'type' => 'INVALIDTYPE');
		$this->expectException('Daytalytics\RequestException', "A Daytalytics\RequestException is thrown when an invalid type request is made to the module.");
		$this->module->module_to_module_request($request);
	}
	
	public function testNoDomainParam() {
		$request = array('module' => 'NortonWebsafe');
		$this->expectException('Daytalytics\RequestException', "A Daytalytics\RequestException is thrown when domain param is not set.");
		$this->module->module_to_module_request($request);
	}
	
	public function testValidSearchSafetyRatingByDomainRequest() {
		$request = array('module' => 'NortonWebsafe', 'type' => 'SafetyRatingByDomain', 'domain' => 'salehoo.com');
		$results = $this->module->module_to_module_request($request);
		$this->assertTrue(is_Array($results));
		$this->assertTrue($results['is_safe'] == 1);
		$this->assertTrue($results['rating'] == 'Safe');
		
		$request = array('module' => 'NortonWebsafe', 'type' => 'SafetyRatingByDomain', 'domain' => 'facebook.com');
		$results = $this->module->module_to_module_request($request);
		$this->assertTrue(is_Array($results));
		$this->assertTrue($results['is_safe'] == 1);
		$this->assertTrue($results['rating'] == 'Norton Secured');
		
		$request = array('module' => 'NortonWebsafe', 'type' => 'SafetyRatingByDomain', 'domain' => 'ywvcomputerprocess.info');
		$results = $this->module->module_to_module_request($request);
		$this->assertTrue(is_Array($results));
		$this->assertTrue($results['is_safe'] == 0);
		$this->assertTrue($results['rating'] == 'Warning');

		$request = array('module' => 'NortonWebsafe', 'type' => 'SafetyRatingByDomain', 'domain' => 'not-existing-website-xxx-yyy.info');
		$results = $this->module->module_to_module_request($request);
		$this->assertTrue(is_Array($results));
		$this->assertTrue($results['is_safe'] == 0);
		$this->assertTrue($results['rating'] == 'Untested');
	}	
}