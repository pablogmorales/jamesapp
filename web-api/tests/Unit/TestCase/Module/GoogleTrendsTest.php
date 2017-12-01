<?php
namespace Daytalytics\Tests\Unit\TestCase\Module;

use Daytalytics\Module\GoogleTrends;
use Daytalytics\Tests\Lib\DbFixtureTestCase;

/**
 *
 * @coversDefaultClass Daytalytics\Module\GoogleTrends
 *
 */
class GoogleTrendsTest extends DbFixtureTestCase {
	
    /**
     * @var GoogleTrends GoogleTrends;
     */
	public $module;

	protected function setUp() {
		parent::setUp();
		$this->module = new GoogleTrends($this->db);
		$this->module->data_sources = array('live' => true, 'local' => false);
	}
	
	public function testIdentity() {
		$this->assertRegExp('/^GoogleTrends$/i', $this->module->identify() , "Module identifier is correct.");
	}
	
	public function testDataUnavailable() {
		$request = array('module' => 'GoogleTrends', 'type' => 'keywordtrenddata', 'keyword' => 'UNKNOWN_KEYWORD_HERE');
		$this->expectException('Daytalytics\RequestException', "A Daytalytics\RequestException is thrown when result is empty.");
		$this->module->module_to_module_request($request);
	}
	
	public function testValidResults() {
		$request = array('module' => 'GoogleTrends', 'type' => 'keywordtrenddata', 'keyword' => 'ipod');
		$results = $this->module->module_to_module_request($request);
		$this->assertTrue(is_array($results));
		$this->assertTrue(is_array($results[0]));
		$this->assertTrue(!empty($results[0]['date_unparsed']));
		$this->assertTrue(!empty($results[0]['time']));
		$this->assertTrue(!empty($results[0]['date']));
		$this->assertTrue(!empty($results[0]['interest']));
	}	
}