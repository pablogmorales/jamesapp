<?php
namespace Daytalytics\Tests\Unit\TestCase\Module;

use Daytalytics\Tests\Lib\DbFixtureTestCase;


class GoogleSearch extends \Daytalytics\Module\GoogleSearch {

	/**
     * Override to get fixture data instead of external request
	 */	
	protected function get_url($keyword, $type, $current_page_start, $loc_name) {
		if ($type == 'NEWS') {
			$html = file_get_contents(dirname(dirname(__DIR__)) . "/Fixture/data/google_news_results.html");
		} else {
			$html = file_get_contents(dirname(dirname(__DIR__)) . "/Fixture/data/google_search_results.html");
		}
		
		// Expect no results
		if($keyword == 'examplewoozlewozzlewiggleexample') {
			$html = '';
		}
		
		return $html;
	}
}


/**
 *
 * @coversDefaultClass Daytalytics\Module\GoogleSearch
 *
 */
class GoogleSearchTest extends DbFixtureTestCase {
	
    /**
     * @var GoogleSearch GoogleSearch;
     */
	public $module;

	protected function setUp() {
		parent::setUp();
		$this->module = new GoogleSearch($this->db);
		$this->module->data_sources = array('live' => true, 'local' => false);
	}
	
	public function testIdentity() {
		$this->assertRegExp('/^GoogleSearch$/i', $this->module->identify() , "Module identifier is correct.");
	}

	protected function helper_test_request($request) {
		$request = array_merge($request, array('data_source' => 'live'));
		return $this->module->handle_request($request);
	}

	public function testNoKeyword() {
		$request = array('module'=>'GoogleSearch');

		$this->expectException('Daytalytics\RequestException', "A Daytalytics\RequestException is thrown when no keyword/Url request is made to the module.");
		$this->helper_test_request($request);
	}

	public function testWithWrongType() {
		$request = array('module'=>'GoogleSearch', 'keyword'=>'ipod', 'type'=>'NP');

		$this->expectException('Daytalytics\RequestException', "A Daytalytics\RequestException is thrown when a not supported type request is made to the module.");
		$this->helper_test_request($request);
	}
	
	public function testNSWithResultUnitedStates() {
		$request = array('module'=>'GoogleSearch', 'type'=>'NS', 'keyword'=>'luxury cars', 'loc'=>'us');
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
		$request = array('module'=>'GoogleSearch', 'type'=>'NS', 'keyword'=>'luxury hotels', 'start'=>7, 'limit'=>12, 'loc'=>'us');

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
		$this->assertTrue($val['Position'] == 7, "Position should be at least greater than 6");
		$this->assertTrue($results['Results'][11]['Position'] == 18, "Last position should be at least less than 19 (7+12)");
		$this->assertFalse(isset($results['Results'][12]), 'There should be no position 19 (would be at index 12)');
	}
	
	public function testNSWithResultLimit0() {
		$request = array('module'=>'GoogleSearch', 'type'=>'NS', 'keyword'=>'luxury cars', 'limit'=>0, 'loc'=>'us');

		$results = $this->helper_test_request($request);
		$this->assertTrue(is_Array($results), "Request returns an array");

		reset($results);
		$this->assertTrue(is_Integer($results['Total']));
		$this->assertTrue(is_Array($results['Results']));

		$val = reset($results['Results']);
		$this->assertEquals($val, false, 'Should be no results or empty array');
	}

	public function testNSWithResultLimit201() {
		$request = array('module'=>'GoogleSearch', 'type'=>'NS', 'keyword'=>'luxury cars', 'limit'=>201, 'loc'=>'us');

		$this->expectException('Daytalytics\RequestException', "A Daytalytics\RequestException is thrown when limit request exceeds maximum is made to the module.");
		$this->helper_test_request($request);
	}

	public function testNSWithNoResultFound() {
		$request = array('module'=>'GoogleSearch', 'type'=>'NS', 'keyword'=>'examplewoozlewozzlewiggleexample', 'loc'=>'us');

		$results = $this->helper_test_request($request);
		$this->assertTrue(is_Array($results), "Request returns an array");

		reset($results);
		$this->assertTrue(is_Integer($results['Total']));
		$this->assertTrue(is_Array($results['Results']));
		$this->assertEmpty($results['Results']);
	}

	
	public function testPSWithResultFound() {
		$request = array('module'=>'GoogleSearch', 'keyword'=>'cheap cars', 'type'=>'PS', 'loc'=>'us');

		$results = $this->helper_test_request($request);
		$this->assertTrue(is_Array($results), "Request returns an array");

		reset($results);
		$this->assertTrue(is_Array($results['Results']));
		$val = $results['Results'][0];
		$this->assertTrue(is_Array($val));
		$this->assertTrue(is_String($val['title']));
		$this->assertTrue(is_String($val['title_html']));
		$this->assertTrue(is_String($val['description1']));
		$this->assertTrue(is_String($val['description2']));
		$this->assertTrue(is_String($val['url']));
		$this->assertTrue(is_String($val['link']));
	}

	public function testPSWithNoResultFound() {
		$request = array('module'=>'GoogleSearch', 'keyword'=>'examplewoozlewozzlewiggleexample', 'type'=>'PS', 'loc'=>'us');

		$results = $this->helper_test_request($request);
		$this->assertTrue(is_Array($results), "Request returns an array");

		reset($results);
		$this->assertTrue(is_Array($results['Results']));
		$this->assertEmpty($results['Results']);
	}

	public function testPLWithResultFound() {
		$request = array('module'=>'GoogleSearch', 'keyword'=>'ipod', 'type'=>'PL', 'loc'=>'us');
		$results = $this->helper_test_request($request);

		$this->assertTrue(is_Integer($results), "Request returns an Integer");
	}

	public function testPLWithNoResultFound() {
		$request = array('module'=>'GoogleSearch', 'keyword'=>'examplewoozlewozzlewiggleexample', 'type'=>'PL', 'loc'=>'us');
		$results = $this->helper_test_request($request);
		$this->assertTrue(is_Integer($results), "Request returns an Integer");
	} 
	
	public function testNEWSWithResult() {
		$request = array('module'=>'GoogleGetter', 'type'=>'News', 'keyword'=>'luxury cars');
	
		$results = $this->helper_test_request($request);
		$this->assertTrue(is_array($results), 'Array', "Request returns an array");
			
		reset($results);
		
		$this->assertTrue(is_array($results['Results']));
	
		$first = reset($results['Results']);
		
		$this->assertTrue(is_String($first['title']));
		$this->assertFalse(empty($first['title']));
		
		$this->assertTrue(is_String($first['title_html']));
		$this->assertFalse(empty($first['title_html']));
		
		$this->assertTrue(is_String($first['link']));
		$this->assertFalse(empty($first['link']));
		$this->assertRegExp('#^https?://#', $first['link']);
		
		$this->assertTrue(is_String($first['excerpt']));
		$this->assertFalse(empty($first['excerpt']));
		
		$this->assertTrue(is_Integer($first['time']));
		$this->assertFalse(empty($first['time']));
		
		$this->assertTrue(is_String($first['source']));
		$this->assertFalse(empty($first['source']));
		
		$this->assertTrue(is_Array($first['additional articles']));
		
		$first_article = reset($first['additional articles']);
		
		$this->assertTrue(is_String($first_article['title']));
		$this->assertFalse(empty($first_article['title']));
		
		$this->assertTrue(is_String($first_article['link']));
		$this->assertFalse(empty($first_article['link']));
		
		$this->assertTrue(is_String($first_article['source']));
		$this->assertFalse(empty($first_article['source']));
	}
	

	public function testGetLocation() {
		$mockLoc = 'us';
		$loc = $this->module->get_location($mockLoc);
		$this->assertTrue($loc == $mockLoc);
		
		$this->expectException('Daytalytics\RequestException', "A Daytalytics\RequestException is thrown when not supported Google location request is made to the module.");
		$loc = $this->module->get_location('kr');
	} 
	
	public function testGetTotal() {
		$html = file_get_contents(dirname(dirname(__DIR__)) . "/Fixture/data/google_search_results.html");
		$actualTotal = $this->module->get_total($html);
		$expectedTotal = 138000000126;
		
		$this->assertTrue($actualTotal == $expectedTotal);
		$this->assertTrue(is_integer($actualTotal), "Request returns an Integer");
		
		// HTML content is empty or incorrect structure
		$actualTotal = $this->module->get_total("");
		$expectedTotal = 0;
		$this->assertTrue($actualTotal == $expectedTotal);
	}
	
	public function testCollectData() {
		$html = file_get_contents(dirname(dirname(__DIR__)) . "/Fixture/data/google_search_results.html");
		$results = $this->module->collectData($html);
		$val = reset($results);
		
		$this->assertTrue(is_Array($val));
		$this->assertTrue(is_String($val['title']));
		$this->assertTrue(is_String($val['title_html']));
		$this->assertTrue(is_String($val['description1']));
		$this->assertTrue(is_String($val['description2']));
		$this->assertTrue(is_String($val['url']));
		$this->assertTrue(is_String($val['link']));
		
		// HTML content is empty or incorrect structure
		$results = $this->module->collectData('');
		$this->assertTrue(empty($results));
	}
	
	
	public function testCollectPaidSearchData() {
		$html = file_get_contents(dirname(dirname(__DIR__)) . "/Fixture/data/google_search_results.html");
		$results = $this->module->collectPaidSearchData($html);
		$val = reset($results);
		
		$this->assertTrue(is_Array($val));
		$this->assertTrue(is_String($val['title']));
		$this->assertTrue(is_String($val['title_html']));
		$this->assertTrue(is_String($val['description1']));
		$this->assertTrue(is_String($val['description2']));
		$this->assertTrue(is_String($val['url']));
		$this->assertTrue(is_String($val['link']));
	}
	
	public function testCollectNewsData() {
		$html = file_get_contents(dirname(dirname(__DIR__)) . "/Fixture/data/google_news_results.html");
		$results = $this->module->collectNewsData($html);
		
		$this->assertTrue(is_Array($results), "Request returns an array");
		reset($results);
		$this->assertTrue(is_Array($results));
		$first = reset($results);
		
		$this->assertTrue(is_String($first['title']));
		$this->assertFalse(empty($first['title']));
		
		$this->assertTrue(is_String($first['title_html']));
		$this->assertFalse(empty($first['title_html']));
		
		$this->assertTrue(is_String($first['link']));
		$this->assertFalse(empty($first['link']));
		$this->assertRegExp('#^https?://#', $first['link']);
		
		$this->assertTrue(is_String($first['excerpt']));
		$this->assertFalse(empty($first['excerpt']));
		
		$this->assertTrue(is_Integer($first['time']));
		$this->assertFalse(empty($first['time']));
		
		$this->assertTrue(is_String($first['source']));
		$this->assertFalse(empty($first['source']));
		
		$this->assertTrue(is_Array($first['additional articles']));
		
		$first_article = reset($first['additional articles']);
		
		$this->assertTrue(is_String($first_article['title']));
		$this->assertFalse(empty($first_article['title']));
		
		$this->assertTrue(is_String($first_article['link']));
		$this->assertFalse(empty($first_article['link']));
		
		$this->assertTrue(is_String($first_article['source']));
		$this->assertFalse(empty($first_article['source']));
		
		
		// HTML content is empty or incorrect structure
		$results = $this->module->collectNewsData('');
		$this->assertTrue(empty($results));
	}
	
	
}
