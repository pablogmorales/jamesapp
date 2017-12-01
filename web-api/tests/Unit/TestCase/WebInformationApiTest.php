<?php
namespace Daytalytics\Tests\Unit\TestCase\Module;

use Daytalytics\WebInformationApi;
use PHPUnit\Framework\TestCase;
use Zend\Diactoros\ServerRequest;

class WebInformationApiTest extends TestCase {
	
    /**
     * @var WebInformationApi WebInformationApi
     */
	public $WebInformationApi;
	
	public function setUp() {
		$this->WebInformationApi = WebInformationApi::get_instance();
	}
	
	public function testGetInstanceReturnsTheSameObject() {
		$webinf1 = WebInformationApi::get_instance();
		$webinf2 = WebInformationApi::get_instance();
		$this->assertEquals($webinf1, $webinf2);
	}
	
	public function testNoRequest() {	
		$results = $this->WebInformationApi->handleRequest(new ServerRequest);
		$this->assertFalse(isset($results['results']));
	}
}
