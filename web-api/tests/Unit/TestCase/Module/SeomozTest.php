<?php
namespace Daytalytics\Tests\Unit\TestCase\Module;

use Daytalytics\Module\Seomoz;
use Daytalytics\Tests\Lib\DbFixtureTestCase;

/**
 *
 * @coversDefaultClass Daytalytics\Module\Seomoz
 *
 */
class SeomozTest extends DbFixtureTestCase {
	
    /**
     * @var Seomoz Seomoz;
     */
	public $module;

	protected function setUp() {
		parent::setUp();
		$this->module = new Seomoz($this->db);
		$this->module->data_sources = array('live' => true, 'local' => false);
		$this->url_metric_data = $this->getTestData('url_metrics.json');
		$this->link_data = $this->getTestData('link_data.json');
		$this->anchor_text_data = array (
		    'phrase_to_page' => $this->getTestData('anchor_text_data_phrase_to_page.json'),
		    'term_to_domain' => $this->getTestData('anchor_text_data_term_to_domain.json'),
		);
	}
	
	public function testIdentity() {
		$this->assertRegExp('/^Seomoz$/i', $this->module->identify() , "Module identifier is correct.");
	}
	
	protected function getTestData($filename) {
	    return file_get_contents(dirname(dirname(__DIR__)) . "/Fixture/data/seomoz_{$filename}");
	}

	protected function getValidParamsForUrlMetricsRequest() {
		return array (
			'url' => 'pear.php.net',
			'type' => 'url-metrics',
		);
	}

	protected function getValidParamsForLinkDataRequest() {
		return array (
			'url' => 'pear.php.net',
			'type' => 'link-data',
		);
	}

	protected function getValidParamsForAnchorTextDataRequest() {
		return array (
			'url' => 'pear.php.net',
			'type' => 'anchor-text-data',
		);
	}

	public function testFullIdentityIsProperlyFormatted() {
		$fullId = $this->module->identify(true);
		$this->assertTrue(is_array($fullId));
		$this->assertTrue(array_key_exists('name', $fullId));
		$this->assertTrue(array_key_exists('input', $fullId));
	}

	public function testSuccessfulUrlMetricsRequest() {
		$params = $this->getValidParamsForUrlMetricsRequest();
		$this->assertNotNull($this->module->__invoke($params));
	}

	public function testHandleRequestThrowsIfNoTypeSpecified() {
		$this->expectException('Daytalytics\RequestException');
		$params = $this->getValidParamsForUrlMetricsRequest();
		unset($params['type']);
		$this->module->__invoke($params);
	}

	public function testHandleRequestThrowsIfInvalidTypeSpecified() {
		$this->expectException('Daytalytics\RequestException');
		$params = $this->getValidParamsForUrlMetricsRequest();
		$params['type'] = 'something-invalid';
		$this->module->__invoke($params);
	}

	public function testHandleRequestThrowsOnUrlMetricsRequestIfNoUrlSpecified() {
		$this->expectException('Daytalytics\RequestException');
		$params = $this->getValidParamsForUrlMetricsRequest();
		unset($params['url']);
		$this->module->__invoke($params);
	}

	public function testHandleRequestThrowsOnLinkDataRequestIfNoUrlSpecified() {
		$this->expectException('Daytalytics\RequestException');
		$params = $this->getValidParamsForLinkDataRequest();
		unset($params['url']);
		$this->module->__invoke($params);
	}

	public function testHandleRequestThrowsOnLinkDataRequestForInvalidSort() {
		$this->expectException('Daytalytics\RequestException');
		$params = $this->getValidParamsForLinkDataRequest();
		$params['sort'] = 'something-invalid';
		$this->module->__invoke($params);
	}

	public function testHandleRequestThrowsOnLinkDataRequestForInvalidScope() {
		$this->expectException('Daytalytics\RequestException');
		$params = $this->getValidParamsForLinkDataRequest();
		$params['scope'] = 'something-invalid';
		$this->module->__invoke($params);
	}

	public function testHandleRequestThrowsOnLinkDataRequestForInvalidFilter() {
		$this->expectException('Daytalytics\RequestException');
		$params = $this->getValidParamsForLinkDataRequest();
		$params['filter'] = 'something-invalid';
		$this->module->__invoke($params);
	}

	public function testHandleRequestThrowsOnLinkDataRequestForInvalidFilterInList() {
		$this->expectException('Daytalytics\RequestException');
		$params = $this->getValidParamsForLinkDataRequest();
		$params['filter'] = 'external,INVALID,follow';
		$this->module->__invoke($params);
	}

	public function testHandleRequestThrowsOnAnchorTextDataRequestIfNoUrlSpecified() {
		$this->expectException('Daytalytics\RequestException');
		$params = $this->getValidParamsForAnchorTextDataRequest();
		unset($params['url']);
		$this->module->__invoke($params);
	}

	public function testHandleRequestThrowsOnAnchorTextDataRequestForInvalidScope() {
		$this->expectException('Daytalytics\RequestException');
		$params = $this->getValidParamsForAnchorTextDataRequest();
		$params['scope'] = 'something-invalid';
		$this->module->__invoke($params);
	}

	public function testParseUrlMetricsReturnsTheExpectedFormat() {
		$results = $this->module->parse_url_metrics($this->url_metric_data);
		$this->assertTrue(is_array($results));

		//just test a couple of these directly then test we have the right number
		$this->assertFalse(empty($results['subdomain-mozrank-pretty']));
		$this->assertFalse(empty($results['page-authority']));
		$this->assertEquals(15, count($results));
	}

	public function testParseLinkDataReturnsTheExpectedFormat() {
		$results = $this->module->parse_link_data($this->link_data);
		$this->assertTrue(is_array($results));
		$this->assertEquals(250, count($results));

		foreach($results as $result) {
			$this->assertEquals(47, count($result));

			//just test a couple of the keys
			$this->assertArrayHasKey('root-domain', $result);
			$this->assertArrayHasKey('link-domain', $result);
		}

	}

	public function testParseAnchorTextDataReturnsTheExpectedFormat() {
		$results = $this->module->parse_anchor_text_data($this->anchor_text_data['phrase_to_page'], 'phrase_to_page');
		$this->assertTrue(is_array($results));
		$this->assertEquals(3, count($results));
		foreach($results as $result) {
			$this->assertEquals(8, count($result));
			$this->assertTrue(isset($result['external-mozrank-passed']));
			$this->assertTrue(isset($result['external-pages-linking']));
		}

		$results = $this->module->parse_anchor_text_data($this->anchor_text_data['term_to_domain'], 'term_to_domain');
		$this->assertTrue(is_array($results));
		$this->assertEquals(3, count($results));
		foreach($results as $result) {
			$this->assertEquals(8, count($result));
			$this->assertTrue(isset($result['external-mozrank-passed']));
			$this->assertTrue(isset($result['external-pages-linking']));
		}
	}
}
