<?php
namespace Daytalytics\Tests\Unit\TestCase\Module;

use Daytalytics\Tests\Lib\DbFixtureTestCase;

class Alexa extends \Daytalytics\Module\Alexa {

    public $test_data;

    public function get($url, $post_vars = null, &$info=array(), $options=array(), $retry_options=array(), $proxy_options=array()) {
        if (isset($this->test_data)) {
            return $this->test_data;
        }
        else {
            $args = func_get_args();
            return call_user_func_array(array('parent', 'get'), $args);
        }
    }
}

/**
 *
 * @coversDefaultClass Daytalytics\Module\Alexa
 *
 */
class TrafficrankTest extends DbFixtureTestCase {
	
    /**
     * @var TestTrafficRank TestTrafficRank
     */
	public $module;

	protected function setUp() {
		parent::setUp();
		$this->module = new Alexa($this->db);
		$this->module->data_sources = array('live' => true, 'local' => false);
	}
	
	public function testIdentity() {
		$this->assertRegExp('/^Alexa$/i', $this->module->identify() , "Module identifier is correct.");
	}

	public function testTrafficRank() {
		$url = 'nasa.com';

		$this->module->domain = $url;
		$rank = $this->module->get_alexa_rank();
		$this->assertTrue(is_Integer($rank));
	}

	public function testSetDomainParsesOutTheDomain() {
		$test_urls = array(
			"http://www.google.com",
			"http://www.google.com/foo/bar/",
			"http://www.google.com/foo/bar",
			"http://www.google.com/search?client=ubuntu&channel=fs&q=preg_match+question+mark&ie=utf-8&oe=utf-8",
			"www.google.com/search?client=ubuntu&/foo/bar/channel=fs&q=preg_match+question+mark&ie=utf-8&oe=utf-8",
			"www.google.com?",
		);

		foreach($test_urls as $url) {
			$this->module->set_domain($url);
			$this->assertEquals("www.google.com", $this->module->domain);
		}
	}

	function testTrafficRankWithNoResult() {
		$url = 'a.domain.that.really.doesnt.exist';

		$this->module->domain = $url;
		$this->expectException('Daytalytics\RequestException');
		$this->module->get_alexa_rank();
	}
	
	public function testTrafficRankWithTimeoutError() {
		$this->module->test_data = '<?xml version="1.0"?>
<aws:UrlInfoResponse xmlns:aws="http://alexa.amazonaws.com/doc/2005-10-05/"><aws:Response><aws:ResponseStatus><aws:StatusCode>TimeoutError</aws:StatusCode><aws:StatusMessage>Request could not be executed due to a connection timeout</aws:StatusMessage></aws:ResponseStatus></aws:Response></aws:UrlInfoResponse>';
		$this->module->data_sources = array_fill_keys(array_keys($this->module->data_sources), false);
		$this->module->data_sources['live'] = true;
		
		$this->expectException('Daytalytics\RequestException');
		$this->module->get_alexa_rank();
	}
	
	public function testTrafficRankWithAlexaError() {
		$this->module->test_data = '<?xml version="1.0"?>
<aws:UrlInfoResponse xmlns:aws="http://alexa.amazonaws.com/doc/2005-10-05/"><aws:Response><aws:ResponseStatus><aws:StatusCode>AlexaError</aws:StatusCode><aws:StatusMessage>Request could not be execute due to an Alexa error</aws:StatusMessage></aws:ResponseStatus></aws:Response></aws:UrlInfoResponse>';
		$this->module->data_sources = array_fill_keys(array_keys($this->module->data_sources), false);
		$this->module->data_sources['live'] = true;
		
		$this->expectException('Daytalytics\RequestException');
		$this->module->get_alexa_rank();
	}
	
	public function testTrafficRankWithServiceUnavailable() {
		$this->module->test_data = '<?xml version="1.0"?>
<Response><Errors><Error><Code>ServiceUnavailable</Code><Message>Service AlexaWebInfoService is currently unavailable. Please try again later</Message></Error></Errors><RequestID>9e0d5db5-06ce-bb19-f0a0-5f2551e69f77</RequestID></Response>';
		$this->module->data_sources = array_fill_keys(array_keys($this->module->data_sources), false);
		$this->module->data_sources['live'] = true;
		
		$this->expectException('Daytalytics\RequestException');
		$this->module->get_alexa_rank();
	}
}
