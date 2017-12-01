<?php
namespace Daytalytics\Tests\Unit\TestCase\Module;

use Daytalytics\Module\BingSearch;
use Daytalytics\Tests\Lib\DbFixtureTestCase;

/**
 *
 * @coversDefaultClass Daytalytics\Module\BingSearch
 *
 */
class BingSearchTest extends DbFixtureTestCase {
	
    /**
     * @var BingSearch BingSearch;
     */
	public $module;

	protected function setUp() {
		parent::setUp();
		$this->module = new BingSearch($this->db);
	}
	
	public function testIdentity() {
		$this->assertRegExp('/^BingSearch$/i', $this->module->identify() , "Module identifier is correct.");
	}
	
	protected function helper_test_request($request) {
		$request = array_merge($request, array('data_source'=>'live'));
		return $this->module->module_to_module_request($request);
	}

	public function testNoKeyword() {
		$request = array('module'=>'BingSearch');

		$this->expectException('Daytalytics\RequestException', "A Daytalytics\RequestException is thrown when no keyword/Url request is made to the module.");
		$this->helper_test_request($request);
	}

	public function testWithWrongType() {
		$request = array('module'=>'BingSearch', 'keyword'=>'ipod', 'type'=>'NP');

		$this->expectException('Daytalytics\RequestException', "A Daytalytics\RequestException is thrown when a not supported type request is made to the module.");
		$this->helper_test_request($request);
	}

	public function testNSWithResultFound() {
		$request = array('module'=>'BingSearch', 'type'=>'NS', 'keyword'=>'luxury cars');

		$results = $this->helper_test_request($request);
		$this->assertTrue(is_Array($results), "Request returns an array");

		reset($results);
		$this->assertTrue(is_Integer($results['Total']));
		$this->assertTrue(is_Array($results['Results']));

		$val = $results['Results'][0];
		$this->assertTrue(is_Array($val));

		$this->assertTrue(is_String($val['Title']));
		$this->assertTrue(is_String($val['Url']));
		$this->assertTrue(is_Integer($val['Position']));
	}

	public function testNSWithResultAndStart7Limit12() {
		$request = array('module'=>'BingSearch', 'type'=>'NS', 'keyword'=>'luxury cars', 'start'=>7, 'limit'=>12);

		$results = $this->helper_test_request($request);
		$this->assertTrue(is_Array($results), "Request returns an array");

		reset($results);
		$this->assertTrue(is_Integer($results['Total']));
		$this->assertTrue(is_Array($results['Results']));

		$val = $results['Results'][0];
		$this->assertTrue(is_Array($val));

		$this->assertTrue(is_String($val['Title']));
		$this->assertTrue(is_String($val['Url']));
		$this->assertTrue(is_Integer($val['Position']));
		$this->assertTrue(count($results['Results']) == 12, "Limit should be equal to 12");
		$this->assertTrue($val['Position'] >= 7, "Position should be at least greater than 6");
		$this->assertTrue($results['Results'][11]['Position'] <= 18, "Last position should be at least less than 19 (7+12)");
		$this->assertTrue(empty($results['Results'][12]['Position']), 'There should be no position 19');
	}

	public function testNSWithResultLimit0() {
		$request = array('module'=>'BingSearch', 'type'=>'NS', 'keyword'=>'luxury cars', 'limit'=>0);
		$results = $this->helper_test_request($request);
		$this->assertTrue(is_Array($results), "Request returns an array");

		reset($results);
		$this->assertTrue(is_Integer($results['Total']));
		$this->assertTrue(is_Array($results['Results']));
		$this->assertTrue(empty($results['Results'][0]),'Should be no results or empty array');
	}

	public function testNSWithResultLimit201() {
		$request = array('module'=>'BingSearch', 'type'=>'NS', 'keyword'=>'luxury cars', 'limit'=>201);

		$this->expectException('Daytalytics\RequestException', "A Daytalytics\RequestException is thrown when limit request exceeds maximum is made to the module.");
		$request_results = $this->helper_test_request($request);
	}

	public function testNSWithNoResultFound() {
		$request = array('module'=>'BingSearch', 'type'=>'NS', 'keyword'=>'examplewoozlewozzlewiggleexample');

		$results = $this->helper_test_request($request);
		$this->assertTrue(is_Array($results), "Request returns an array");

		$this->assertTrue(is_Integer($results['Total']));
		$this->assertTrue(is_Array($results['Results']));

		$this->assertEquals($results['Total'], 0);
		$this->assertEquals(count($results['Results']), 0);
	}

	public function testPLWithResultFound() {
		$request = array('module'=>'BingSearch', 'keyword'=>'ipod', 'type'=>'PL');
		$results = $this->helper_test_request($request);
		$this->assertTrue(is_Integer($results), "Request returns an Integer");
	}

	public function testPLWithNoResultFound() {
		$request = array('module'=>'BingSearch', 'keyword'=>'examplewoozlewozzlewiggleexample', 'type'=>'PL');
		$results = $this->helper_test_request($request);
		$this->assertTrue(is_Integer($results), "Request returns an Integer");
	}
	
	public function testNSWithResultFoundNewZealand() {
		$request = array('module'=>'BingSearch', 'type'=>'NS', 'keyword'=>'luxury cars', 'loc'=>'New Zealand');
		$request_us = array('module'=>'BingSearch', 'type'=>'NS', 'keyword'=>'luxury cars', 'loc'=>'UNiTED STATES');
		
		$results = $this->helper_test_request($request);
		$results_us = $this->helper_test_request($request_us);
		
		$this->assertTrue(is_Array($results), "Request returns an array");

		reset($results);
		$this->assertTrue(is_Integer($results['Total']));
		$this->assertTrue(is_Array($results['Results']));

		$val = $results['Results'][0];
		$this->assertTrue(is_Array($val));

		$this->assertTrue(is_String($val['Title']));
		$this->assertTrue(is_String($val['Url']));
		$this->assertTrue(is_Integer($val['Position']));
		
		$title_nz = $results['Results'][0]['Title'];
		$title_us = $results_us['Results'][0]['Title'];
		$this->assertTrue($title_nz <> $title_us, "New Zealand, US first position title shouldn't be the same.");
	}
}
