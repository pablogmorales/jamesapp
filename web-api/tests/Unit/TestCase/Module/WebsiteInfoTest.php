<?php
namespace Daytalytics\Tests\Unit\TestCase\Module;

use Daytalytics\Module\WebsiteInfo;
use Daytalytics\Tests\Lib\DbFixtureTestCase;
use DOMDocument;

/**
 *
 * @coversDefaultClass Daytalytics\Module\WebsiteInfo
 *
 */
class WebsiteInfoTest extends DbFixtureTestCase {
	
    /**
     * @var WebsiteInfo WebsiteInfo;
     */
	public $module;

	protected function setUp() {
		parent::setUp();
		$this->module = new WebsiteInfo($this->db);
		$this->module->data_sources = array('live' => true, 'local' => false);
	}
	
	public function testIdentity() {
		$this->assertRegExp('/^WebsiteInfo$/i', $this->module->identify() , "Module identifier is correct.");
	}

    public function testInvalidType() {
        $request = ['module' => 'WebsiteInfo', 'type' => 'INVALIDTYPE'];
        $this->expectException('Daytalytics\RequestException', "A Daytalytics\RequestException is thrown when an invalid type request is made to the module.");
        $this->module->module_to_module_request($request);
    }

    public function testNoDomainParam() {
        $request = ['module' => 'WebsiteInfo'];
        $this->expectException('Daytalytics\RequestException', "A Daytalytics\RequestException is thrown when domain param is not set.");
        $this->module->module_to_module_request($request);
    }

    public function testNoAddressPhoneInSchema() {
        $testHtml = "<p>This is only text, it doesn't contain telephone and address schemas</p>";
        $domHtml = new DOMDocument('1.0', 'UTF-8');
        $domHtml->preserveWhitespace = true;
        @$domHtml->loadHTML($testHtml);

        $results = [
            'phones' => $this->module->parsePhoneNumbers($domHtml),
            'addresses' => $this->module->parseAddresses($domHtml)
        ];
        $expected = ['phones' => [], 'addresses' => []];
        $this->assertEquals($results, $expected);
    }

    public function testExistingAddressPhoneInSchema() {
        $testHtml = '<p>Before Text</p><span itemprop="telephone"><span>(123) 456-0897</span></span><p>Inserted Text</p>'.
                    '<div itemprop="address" itemtype="http://schema.org/PostalAddress"><span>Main Street</span><span> California, CA</span></div>'.
                    '<div itemprop=\'telephone\'>(009)432-0933</div><div>End Text</div>';
        $domHtml = new DOMDocument('1.0', 'UTF-8');
        $domHtml->preserveWhitespace = true;
        @$domHtml->loadHTML($testHtml);

        $results = [
            'phones' => $this->module->parsePhoneNumbers($domHtml),
            'addresses' => $this->module->parseAddresses($domHtml)
        ];
        $expected = [
            'phones' => ['(123) 456-0897', '(009)432-0933'],
            'addresses' => ['Main Street California, CA']
        ];
        $this->assertEquals($results, $expected);
    }

    public function testNoAddressPhoneLinks() {
        $testHtml = "<p>This is only text, it doesn't contain contact links in page content.</p>";
        $domHtml = new DOMDocument('1.0', 'UTF-8');
        $domHtml->preserveWhitespace = true;
        @$domHtml->loadHTML($testHtml);

        $results = $this->module->parseAboutContactUrls($domHtml);
        $expected = [];
        $this->assertEquals($results, $expected);
    }

    public function testExistingAddressPhoneLinks() {
        $testHtml = '<p>Before text</p><a href="http://example.com/contact.php">Contact Us</a>'.
                    '<a href="http://example.com/about.php">About</a><p>After Text</p>';
        $domHtml = new DOMDocument('1.0', 'UTF-8');
        $domHtml->preserveWhitespace = true;
        @$domHtml->loadHTML($testHtml);

        $this->module->setParams(['domain' => 'example.com']);
        $results = $this->module->parseAboutContactUrls($domHtml);
        $expected = ['http://example.com/contact.php', 'http://example.com/about.php'];
        $this->assertEquals($results, $expected);
    }
}