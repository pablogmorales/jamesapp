<?php
namespace Daytalytics\Tests\Unit\TestCase\Module;

use Daytalytics\Tests\Lib\DbFixtureTestCase;


class ProductIdeas extends \Daytalytics\Module\ProductIdeas {

	/**
	 * Override to get fixture data instead of external request
	 */
	public function getCategories(array $params = []) {
		$xml = '';
		switch($params['feed']) {
			case 'wishedlist':
			case 'amazon':
				$xml = file_get_contents(dirname(dirname(__DIR__)) . "/Fixture/data/product_ideas/amazon.xml");
				break;
			case 'ali':
				$xml = file_get_contents(dirname(dirname(__DIR__)) . "/Fixture/data/product_ideas/ali.xml");
				break;
		}
		return $xml;
	}
}

/**
 *
 * @coversDefaultClass Daytalytics\Module\ProductIdeas
 *
 */
class ProductIdeasTest extends DbFixtureTestCase {
	
    /**
     * @var ProductIdeas ProductIdeas;
     */
	public $module;
	
	protected function setUp() {
		parent::setUp();
	    $this->module = new ProductIdeas($this->db);
	    $this->module->data_sources = array('live' => true, 'local' => false);
	}
	
	public function testIdentity() {
		$this->assertRegExp('/^ProductIdeas$/i', $this->module->identify(), "Module identifier is correct.");
	}
	
	public function testInvalidType() {
		$request = array('module' => 'ProductIdeas', 'type' => 'INVALIDTYPE');
		$this->expectException('Daytalytics\RequestException', "A Daytalytics\Daytalytics\RequestException is thrown when an invalid type request is made to the module.");
		$results = $this->module->module_to_module_request($request);
	}
	
	public function testInvalidFeed() {
		$request = array('module' => 'ProductIdeas', 'type' => 'categories', 'feed' => 'INVALID FEED');
		$this->expectException('Daytalytics\RequestException', "A Daytalytics\Daytalytics\RequestException is thrown when an invalid feed request is made to the module.");
		$results = $this->module->module_to_module_request($request);
	}
	
	public function testAmazonRequest() {
		$request = array('module' => 'ProductIdeas', 'type' => 'categories', 'feed' => 'amazon');
		$results = $this->module->module_to_module_request($request);
		$this->_testResults($results);
	}
	
	public function testAliRequest() {
		$request = array('module' => 'ProductIdeas', 'type' => 'categories', 'feed' => 'ali');
		$results = $this->module->module_to_module_request($request);
		$this->_testResults($results);
	}
	
	protected function _testResults($results) {
		$this->assertTrue(is_array($results['root']['cat'][0]['product']));
		$this->assertTrue(is_array($results['root']['cat'][0]['product'][0]));
		$this->assertArrayHasKey('id', $results['root']['cat'][0]['product'][0]);
		$this->assertArrayHasKey('cat_id', $results['root']['cat'][0]['product'][0]);
		$this->assertArrayHasKey('title', $results['root']['cat'][0]['product'][0]);
		$this->assertArrayHasKey('sales_rank', $results['root']['cat'][0]['product'][0]);
		$this->assertArrayHasKey('img_url', $results['root']['cat'][0]['product'][0]);
		$this->assertArrayHasKey('asin', $results['root']['cat'][0]['product'][0]);
		$this->assertArrayHasKey('amazon_link', $results['root']['cat'][0]['product'][0]);
		$this->assertArrayHasKey('price', $results['root']['cat'][0]['product'][0]);
		$this->assertArrayHasKey('offers', $results['root']['cat'][0]['product'][0]);
	}
	
	
}