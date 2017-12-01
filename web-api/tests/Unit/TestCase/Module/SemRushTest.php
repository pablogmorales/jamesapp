<?php
namespace Daytalytics\Tests\Unit\TestCase\Module;

use Daytalytics\Module\SemRush;
use Daytalytics\Tests\Lib\DbFixtureTestCase;

/**
 *
 * @coversDefaultClass Daytalytics\Module\SemRush
 *
 */
class SemRushTest extends DbFixtureTestCase {
	
    /**
     * @var SemRush SemRush
     */
	public $module;

	protected function setUp() {
		parent::setUp();
		$this->module = new SemRush($this->db);
		$this->module->data_sources = array('live' => true, 'local' => false);
		$this->keyword_report_test_data = $this->getTestData('keyword_report.csv');
		$this->related_keywords_test_data = $this->getTestData('related_keywords.csv');
	}
	
	public function testIdentity() {
		$this->assertRegExp('/^SemRush$/i', $this->module->identify() , "Module identifier is correct.");
	}

    private function get_valid_request_params() {
        return array(
            'keyword' => 'foo',
            'db_name' => 'us',
            'type' => 'keyword_report'
        );
    }
    
    protected function getTestData($filename) {
        return file_get_contents(dirname(dirname(__DIR__)) . "/Fixture/data/sem_rush_{$filename}");
    }

    public function testHandleRequestThrowsIfNoDBNameSpecified() {
        $this->expectException('Daytalytics\RequestException');
        $params = $this->get_valid_request_params();
        $params['db_name'] = null;
        $this->module->handle_request($params);
    }

    public function testHandleRequestThrowsIfNoDBNameNotInList() {
        $this->expectException('Daytalytics\RequestException');
        $params = $this->get_valid_request_params();
        $params['db_name'] = 'foobar';
        $this->module->handle_request($params);
    }

    public function testHandleRequestThrowsIfKeywordSpecified() {
        $this->expectException('Daytalytics\RequestException');
        $params = $this->get_valid_request_params();
        $params['keyword'] = null;
        $this->module->handle_request($params);
    }

    public function testFullIdentityIsProperlyFormatted() {
        $fullId = $this->module->identify(true);
        $this->assertTrue(is_array($fullId));
        $this->assertTrue(array_key_exists('name', $fullId));
        $this->assertTrue(array_key_exists('input', $fullId));
    }

    public function testParseMainKeywordReportDataReturnsExpectedResults() {
        $result = $this->module->parse_keyword_report_data($this->keyword_report_test_data);
        $this->assertEquals($result['phrase'], 'guns');
        $this->assertEquals($result['average_num_queries_per_month'], '902068');
        $this->assertEquals($result['average_adword_cost_per_click'], '0.68');
        $this->assertEquals($result['advertising_competition'], '7.03');
        $this->assertEquals($result['num_pages'], '80100000');
        $this->assertEquals($result['trend_data'], '0.91,0.93,0.99,0.90,0.96,0.86,0.85,0.77,0.75,0.74,0.78,0.82');
    }

    public function testParseRelatedKeywordsReturnsExpectedResults() {
        $results = $this->module->parse_related_keywords_report_data($this->related_keywords_test_data);
        $this->assertEquals(count($results), 10);
       
        //test the fields of the 5th result
        $test_data = $results[4];
        $this->assertEquals($test_data['phrase'], 'where to buy firearms');
        $this->assertEquals($test_data['average_num_queries_per_month'], '43');
        $this->assertEquals($test_data['average_adword_cost_per_click'], '0.05');
        $this->assertEquals($test_data['advertising_competition'], '1.84');
        $this->assertEquals($test_data['num_pages'], '4910000');
        $this->assertEquals($test_data['trend_data'], '0.39,0.42,0.51,0.32,0.48,0.61,0.78,0.99,0.60,0.65,0.83,0.78');
    }

    public function testRelatedKeywordsReportUrlForReturnsExpectedResults() {
        $actual_url = $this->module->related_keywords_report_url_for('FOO', 'BAR', 100, 50);
        $expected_url = "http://api.semrush.com?key=" . SemRush::API_KEY . "&database=FOO&export_columns=Ph%2CNq%2CCp%2CCo%2CNr%2CTd&type=phrase_related&phrase=BAR&display_offset=100&display_limit=150";
        $this->assertEquals($actual_url, $expected_url);
        
    }

    public function testKeywordReportRequestUrlForReturnsExpectedResults() {
        $actual_url = $this->module->keyword_report_request_url_for('FOO', 'BAR');
        $expected_url = "http://api.semrush.com?key=" . SemRush::API_KEY . "&database=FOO&export_columns=Ph%2CNq%2CCp%2CCo%2CNr%2CTd&type=phrase_this&phrase=BAR";
        $this->assertEquals($actual_url, $expected_url);
    }
}