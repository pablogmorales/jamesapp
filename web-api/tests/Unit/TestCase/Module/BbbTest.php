<?php
namespace Daytalytics\Tests\Unit\TestCase\Module;

use Daytalytics\Module\Bbb;
use Daytalytics\Tests\Lib\DbFixtureTestCase;

/**
 *
 * @coversDefaultClass Daytalytics\Module\Bbb
 *
 */
class BbbTest extends DbFixtureTestCase {
	
    /**
     * @var Bbb Bbb;
     */
	public $Module;

	protected function setUp() {
		parent::setUp();
		$this->module = new Bbb($this->db);
		$this->module->data_sources = array('live' => true, 'local' => false);
	}
	
	public function testIdentity() {
		$this->assertRegExp('/^BBB$/i', $this->module->identify() , "Module identifier is correct.");
	}
	
	public function testInvalidType() {
		$request = array('module' => 'BBB', 'type' => 'INVALIDTYPE');
		$this->expectException('Daytalytics\RequestException', "A Daytalytics\RequestException is thrown when an invalid type request is made to the module.");
		$this->module->module_to_module_request($request);
	}
	
	public function testNoDomainParam() {
		$request = array('module' => 'BBB');
		$this->expectException('Daytalytics\RequestException', "A Daytalytics\RequestException is thrown when domain param is not set.");
		$this->module->module_to_module_request($request);
	}
	
	public function testValidSearchOrginizationByDomainRequest() {
		$request = array('module' => 'BBB', 'type' => 'OrganizationalSearchByDomain', 'domain' => 'salehoo.com');
		$results = $this->module->module_to_module_request($request);
		$this->assertTrue(is_array($results));
		$this->assertTrue(!empty($results['BusinessId']));
		$this->assertTrue(!empty($results['OrganizationName']));
		$this->assertTrue(!empty($results['PrimaryCategory']));
		$this->assertTrue(!empty($results['City']));
		$this->assertTrue(!empty($results['StateProvince']));
		$this->assertTrue(!empty($results['Phones']));
		$this->assertTrue(!empty($results['PostalCode']));
		$this->assertTrue(!empty($results['BusinessURLs']));
		$this->assertTrue(!empty($results['Address']));	
	}
	
	public function testInvalidSearchOrginizationByDomainRequest() {
		$request = array('module' => 'BBB', 'type' => 'OrganizationalSearchByDomain', 'domain' => 'salehoosss.com');
		$this->expectException('Daytalytics\RequestException', "A Daytalytics\RequestException is thrown when an BBB record is not found");
		$this->module->module_to_module_request($request);
	}
}
