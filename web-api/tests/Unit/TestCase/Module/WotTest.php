<?php
namespace Daytalytics\Tests\Unit\TestCase\Module;

use Daytalytics\Module\Wot;
use Daytalytics\Tests\Lib\DbFixtureTestCase;

/**
 *
 * @coversDefaultClass Daytalytics\Module\Wot
 *
 */
class WotTest extends DbFixtureTestCase {
	
    /**
     * @var Wot Wot;
     */
	public $module;

	protected function setUp() {
		parent::setUp();
		$this->module = new Wot($this->db);
		$this->module->data_sources = array('live' => true, 'local' => false);
	}
	
	public function testIdentity() {
		$this->assertRegExp('/^Wot$/i', $this->module->identify() , "Module identifier is correct.");
	}
	
    public function testInvalidType() {
        $request = array('module' => 'Wot', 'type' => 'INVALIDTYPE');
        $this->expectException('Daytalytics\RequestException', "A Daytalytics\RequestException is thrown when an invalid type request is made to the module.");
        $this->module->module_to_module_request($request);
    }

    public function testNoDomainParam() {
        $request = array('module' => 'Wot');
        $this->expectException('Daytalytics\RequestException', "A Daytalytics\RequestException is thrown when domain param is not set.");
        $this->module->module_to_module_request($request);
    }

    public function testValidSearch() {
        $request = array('module' => 'Wot', 'type' => 'DomainTrustworthinessMetric', 'domain' => 'salehoo.com');
        $results = $this->module->module_to_module_request($request);

        $trustWorthinessExpected = array(
            'score' => 63,
            'reputation' => 'Good',
            'confidence' => 30
        );
        $childSafetyExpected = array(
            'score' => 63,
            'reputation' => 'Good',
            'confidence' => 25
        );
        $categoryExpected = array(
            'group' => 'Positive',
            'description' => 'Good site',
            'confidence' => 30
        );
        $this->assertEquals($results['trustworthiness'], $trustWorthinessExpected);
        $this->assertEquals($results['childsafety'], $childSafetyExpected);
        $this->assertEquals($results['category'], $categoryExpected);
    }

}
