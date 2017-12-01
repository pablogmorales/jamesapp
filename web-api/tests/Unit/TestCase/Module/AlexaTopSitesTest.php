<?php
namespace Daytalytics\Tests\Unit\TestCase\Module;

use Daytalytics\Module\AlexaTopSites;
use Daytalytics\Tests\Lib\DbFixtureTestCase;

/**
 *
 * @coversDefaultClass Daytalytics\Module\AlexaTopSites
 *
 */
class AlexaTopSitesTest extends DbFixtureTestCase {
	
    /**
     * @var AlexaTopSites AlexaTopSites;
     */
	public $module;

	protected function setUp() {
		parent::setUp();
		$this->module = new AlexaTopSites($this->db);
		$this->module->data_sources = array('live' => true, 'local' => false);
	}
	
	public function testIdentity() {
		$this->assertRegExp('/^AlexaTopSites$/i', $this->module->identify() , "Module identifier is correct.");
	}
	
	public function testTopSites() {
		$request = array('module' => 'AlexaTopSites', 'countryCode' => 'us');
		$results = $this->module->module_to_module_request($request);
		
		$this->assertTrue(is_array($results));
		$this->assertTrue(count($results) == 100);
		$this->assertTrue(in_array('google.com', $results));
		$this->assertTrue(in_array('facebook.com', $results));
		$this->assertTrue(in_array('youtube.com', $results));	
	}
	
	public function testTopSitesWithLimit() {
		$limit = 10;
		$request = array('module' => 'AlexaTopSites', 'countryCode' => 'ph', 'numReturn' => $limit);
		$results = $this->module->module_to_module_request($request);
		$this->assertTrue(count($results) == $limit);
	}
		
	public function testTopSitesInvalidCountryCode() {
		$request = array('module' => 'AlexaTopSites', 'countryCode' => 'NOT-A-COUNTRY-CODE');
		$results = $this->module->module_to_module_request($request);
		$this->assertTrue(empty($results));
	}
	
	public function testTopSitesNoCountryCode() {
		$request = array('module' => 'AlexaTopSites');
		$this->expectException('Daytalytics\RequestException', "A Daytalytics\RequestException is thrown when countryCode param is empty.");
		$this->module->module_to_module_request($request);
	}	
}