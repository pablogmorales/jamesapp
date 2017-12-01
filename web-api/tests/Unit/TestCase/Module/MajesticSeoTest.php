<?php
namespace Daytalytics\Tests\Unit\TestCase\Module;

use Daytalytics\Module\MajesticSeo;
use Daytalytics\Tests\Lib\DbFixtureTestCase;

/**
 *
 * @coversDefaultClass Daytalytics\Module\MajesticSeo
 *
 */
class MajesticSeoTest extends DbFixtureTestCase {
	
    /**
     * @var MajesticSeo MajesticSeo;
     */
	public $module;
	
	/**
	 * @var array
	 */
	protected $testData = [];
	

	protected function setUp() {
		parent::setUp();
		$this->module = new MajesticSeo($this->db);
		$this->module->data_sources = array('live' => true, 'local' => false);
		$this->testData = array (
		    'successful' => $this->getTestData('successful.xml'),
		    'error' => $this->getTestData('error.xml'),
		);
	}
	
	public function testIdentity() {
		$this->assertRegExp('/^MajesticSeo$/i', $this->module->identify() , "Module identifier is correct.");
	}

	
	protected function getTestData($filename) {
	    return file_get_contents(dirname(dirname(__DIR__)) . "/Fixture/data/majestic_seo_{$filename}");
	}

	protected function getValidRequestParams() {
		return array (
			'url' => 'pear.php.net',
			'type' => 'backlinks',
		);
	}

	public function testHandleRequestThrowsIfNoTypeIsGiven() {
		$this->expectException('Daytalytics\RequestException');
		$params = $this->getValidRequestParams();
		$params['type'] = null;
		$this->module->handle_request($params);
	}

	public function testHandleRequestThrowsIfNoUrlIsGiven() {
		$this->expectException('Daytalytics\RequestException');
		$params = $this->getValidRequestParams();
		$params['url'] = null;
		$this->module->handle_request($params);
	}

	public function testFullIdentityIsProperlyFormatted() {
		$fullId = $this->module->identify(true);
		$this->assertTrue(is_array($fullId));
		$this->assertTrue(array_key_exists('name', $fullId));
		$this->assertTrue(array_key_exists('input', $fullId));
	}

	public function testGetBacklinksUrlIsProperlyFormatted() {
		$url = $this->module->get_backlinks_url("foo.com?q=bar", "fresh");
		$this->assertRegExp('/item0=foo.com%3Fq%3Dbar/', $url);
		$this->assertRegExp('/app_api_key='. MajesticSeo::API_KEY .'/', $url);
	}

	public function testParseBacklinkDataReturnsExpectedFormat() {
		$data = $this->module->parse_backlink_data($this->testData['successful']);
		$this->assertEquals(31, count($data));
		//just test a couple of keys and assume the rest are fine
		$this->assertTrue(in_array('ResultCode', array_keys($data)));
		$this->assertTrue(in_array('IndexedURLs', array_keys($data)));
	}

	public function testParseBacklinkDataTrimsStrings() {
		$data = $this->module->parse_backlink_data($this->testData['successful']);
		$this->assertEquals("", $data['FinalRedirectResult']);
	}

	public function testPraseBacklinkDataThrowsOnAPIErrors() {
		$this->expectException('Exception');
		$this->module->parse_backlink_data($this->testData['error']);
	}
}
