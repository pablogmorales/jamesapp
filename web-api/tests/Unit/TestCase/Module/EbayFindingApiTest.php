<?php
namespace Daytalytics\Tests\Unit\TestCase\Module;

use Daytalytics\Module\EbayFindingApi;
use Daytalytics\Tests\Lib\DbFixtureTestCase;

/**
 *
 * @coversDefaultClass Daytalytics\Module\EbayFindingApi
 *
 */
class EbayFindingApiTest extends DbFixtureTestCase {

    /**
     * @var EbayFindingApi EbayFindingApi
     */
    public $module;

    protected function setUp() {
		parent::setUp();
        $this->module = new EbayFindingApi($this->db);
    }

    public function testIdentity() {
        $this->assertRegExp('/^EbayFindingApi$/i', $this->module->identify() , "Module identifier is correct.");
    }

	public function testFullIdentityIsProperlyFormatted() {
		$fullId = $this->module->identify(true);
		$this->assertTrue(is_array($fullId));
		$this->assertTrue(array_key_exists('name', $fullId));
		$this->assertTrue(array_key_exists('input', $fullId));
	}

	public function testSuccessfulRequestProducesProperlyFormattedResults() {
		$mock = $this->getMockModule('successful.xml');
		$response = $mock->find_items_by_keywords('test', 'us');
		$first_result = $response[0];

		$this->assertTrue(array_key_exists('itemId', $first_result));
		$this->assertTrue(array_key_exists('viewItemURL', $first_result));
		$this->assertTrue(array_key_exists('galleryURL', $first_result));
		$this->assertTrue(array_key_exists('title', $first_result));

		$listingInfo = $first_result['listingInfo'];
		$this->assertTrue(array_key_exists('listingType', $listingInfo));
		$this->assertTrue(array_key_exists('endTime', $listingInfo));

		$primaryCategory = $first_result['primaryCategory'];
		$this->assertTrue(array_key_exists('categoryId', $primaryCategory));

		$sellingStatus = $first_result['sellingStatus'];
		$this->assertTrue(array_key_exists('bidCount', $sellingStatus));
		$this->assertTrue(array_key_exists('convertedCurrentPrice', $sellingStatus));
		$this->assertTrue(array_key_exists('timeLeft', $sellingStatus));

		$shippingInfo = $first_result['shippingInfo'];
		$this->assertTrue(array_key_exists('shippingServiceCost', $shippingInfo));
		$this->assertTrue(array_key_exists('shippingType', $shippingInfo));
	}

	//if there is an error in our request, an Exception is thrown
	public function testShouldRetryRequestThrowsAnExceptionForRequestErrors(){
		$response_data = $this->getTestData('request_error.xml');
		$this->expectException('Exception', 'An exception is thrown when an invalid request error is received.');
		$this->module->should_retry_request($response_data, array());
	}

	//if something goes wrong on ebays server, try again
	public function testShouldRetryRequestReturnsTrueForServerErrors(){
		$response_data = $this->getTestData('server_error.xml');
		$this->assertTrue($this->module->should_retry_request($response_data, array()));
	}

	public function testShouldRetryRequestReturnsTrueBlankResponses(){
		$this->assertTrue($this->module->should_retry_request('', array()));
	}

	protected function getTestData($filename) {
		return file_get_contents(dirname(dirname(__DIR__)) . "/Fixture/data/ebay_finding_api_{$filename}");
	}

	protected function getMockModule($test_data_filename){
	    $mock = $this->getMockBuilder(get_class($this->module))
	       ->setConstructorArgs([$this->db])
	       ->setMethods(['getEbayData'])
	       ->getMock();
	    $mock->method('getEbayData')
	       ->will($this->returnValue($this->getTestData($test_data_filename)));
		return $mock;
	}
}
