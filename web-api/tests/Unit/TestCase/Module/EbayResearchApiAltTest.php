<?php
namespace Daytalytics\Tests\Unit\TestCase\Module;

use Daytalytics\Module\EbayResearchApiAlt;
use Daytalytics\Tests\Lib\DbFixtureTestCase;

/**
 *
 * @coversDefaultClass Daytalytics\Module\EbayResearchApiAlt
 *
 */
class EbayResearchApiAltTest extends DbFixtureTestCase {

    /**
     * @var EbayResearchApiAlt EbayResearchApiAlt
     */
    public $module;

    protected function setUp() {
		parent::setUp();
        $this->module = new EbayResearchApiAlt($this->db);
    }

    public function testIdentity() {
        $this->assertRegExp('/^EbayResearchApiAlt$/i', $this->module->identify() , "Module identifier is correct.");
    }
	
	public function testNoTypeNoKeyword() {
		$request = array('module'=>'EbayResearchApiAlt');
		
		$this->expectException('Daytalytics\RequestException', "A Daytalytics\RequestException is thrown when a blank request is made to the module.");
		$this->module->module_to_module_request($request);
	}
	
	public function testKeywordNoType() {
		$request = array('module'=>'EbayResearchApiAlt', 'keyword'=>'ipod');
		
		$this->expectException('Daytalytics\RequestException', "A Daytalytics\RequestException is thrown when no type was passed.");
		$this->module->module_to_module_request($request);
	}
	
	public function testKeywordEmptyType() {
		$request = array('module'=>'EbayResearchApiAlt', 'type'=>'', 'keyword'=>'ipod');
		
		$this->expectException('Daytalytics\RequestException', "A Daytalytics\RequestException is thrown when an empty type was passed.");
		$this->module->module_to_module_request($request);
	}
	
	public function testTypeNoKeyword() {
		$request = array('module'=>'EbayResearchApiAlt', 'type'=>'ResearchResults');
		
		$this->expectException('Daytalytics\RequestException', "A Daytalytics\RequestException is thrown when no keyword was passed.");
		$this->module->module_to_module_request($request);
	}
	
	public function testInvalidLoc() {
		$request = array('module'=>'EbayResearchApiAlt', 'type'=>'ResearchResults', 'loc'=>'uk');
		
		$this->expectException('Daytalytics\RequestException', "A Daytalytics\RequestException is thrown when a non-'us' loc was passed.");
		$this->module->module_to_module_request($request);
	}
	
	public function testBadStartDate() {
		$request = array('module' => 'EbayResearchApiAlt', 'type' => 'ResearchResults', 'keyword' => 'ipod', 'start' => '2006-04-25');
		
		$this->expectException('Daytalytics\RequestException', "A Daytalytics\RequestException is thrown when a mid-month start date was passed.");
		$this->module->module_to_module_request($request);
	}
	
	public function testExpectSuccess() {
		$month = date('m', strtotime('-2 months'));
		$year = date('Y', strtotime('-2 months'));
		$request = array('module'=>'EbayResearchApiAlt', 'type'=>'ResearchResults', 'keyword'=>'ipod nanos', 'start' => "$year-$month-01");
		
		$results = $this->module->module_to_module_request($request);
				
		$this->assertTrue(is_array($results), "Request returns an array");
		
		$this->assertTrue(is_array($results['total']));
		$this->assertTrue(is_int($results['total']['number of listings']), "number of listings exists");
		$this->assertTrue(is_int($results['total']['number of successful listings']), "number of successful listings exists");
		$this->assertTrue(is_int($results['total']['number of completed listings']), "number of completed listings exists");
		$this->assertTrue(is_int($results['total']['number of total listings']), "number of total listings exists");
	
	}
}
