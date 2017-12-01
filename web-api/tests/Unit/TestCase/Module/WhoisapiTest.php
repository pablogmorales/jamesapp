<?php
namespace Daytalytics\Tests\Unit\TestCase\Module;

use Daytalytics\Module\WhoisXmlApi;
use Daytalytics\Tests\Lib\DbFixtureTestCase;

/**
 *
 * @coversDefaultClass Daytalytics\Module\WhoisXmlApi
 *
 */
class WhoisapiTest extends DbFixtureTestCase {
	
    /**
     * @var WhoisXmlApi WhoisXmlApi;
     */
	public $module;

	protected function setUp() {
		parent::setUp();
		
		$this->module = new WhoisXmlApi($this->db);
		$this->module->data_sources = array('live' => true, 'local' => false);
		$this->testData = array(
		    'successful' => $this->getTestData('successful.xml'),
		    'unregistered' => $this->getTestData('unregistered_domain.xml')
		);
	}
	
	protected function getTestData($filename) {
	    return file_get_contents(dirname(dirname(__DIR__)) . "/Fixture/data/whoisxmlapi_{$filename}");
	}
	
	public function testIdentity() {
		$this->assertRegExp('/^WhoisXmlApi$/i', $this->module->identify() , "Module identifier is correct.");
	}

	public function testFullIdentityIsProperlyFormatted() {
		$fullId = $this->module->identify(true);
		$this->assertTrue(is_array($fullId));
		$this->assertTrue(array_key_exists('name', $fullId));
		$this->assertTrue(array_key_exists('input', $fullId));
	}

	public function testExtractRawTextFromDataParsesOutRightDataForRegisteredDomain() {
		$result = $this->module->extract_raw_text_from_data($this->testData['successful']);
		$this->assertEquals(1, preg_match('/^Registrant.*AVAILABILITY\.$/s', $result));
	}

	public function testExtractRawTextFromDataParsesOutRightDataForUnregisteredDomain() {
		$result = $this->module->extract_raw_text_from_data($this->testData['unregistered']);
		$this->assertEquals(1, preg_match('/^Whois Server.*Registrars\.$/s', $result));
	}

	public function testExceptionIsThrownWhenNoInputDomainSpecified() {
		$this->expectException('Daytalytics\RequestException');
		$this->module->handle_request(array());
	}

	public function testExtractRawTextFromDataSubsOutHtmlEntities() {
		//when we fetch the data it contains entities, but simplexml seems to 
		//automatically strip them out
		$result = $this->module->extract_raw_text_from_data($this->testData['successful']);
		$this->assertTrue(strpos($result, '&quot;') === false);
	}
}