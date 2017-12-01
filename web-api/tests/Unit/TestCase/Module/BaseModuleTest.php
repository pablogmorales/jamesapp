<?php
namespace Daytalytics\Tests\Unit\TestCase;

use PHPUnit\Framework\TestCase;

/**
 *
 * @coversDefaultClass Daytalytics\BaseModule
 *
 */
class BaseModuleTest extends TestCase {
	
    /**
     * @var BaseModule BaseModule
     */
	public $BaseModule;
	
	public function setUp() {
	    $this->BaseModule = $this->getMockBuilder('Daytalytics\Module\BaseModule')
	       ->disableOriginalConstructor()
	       ->getMockForAbstractClass();
	}
	
	/**
	 * @test
	 * @covers ::parseQuery
	 */
	public function testParseQuery() {
		$query = array(
			'data' => array(
				'model' => array(
					'field' => 'value',
					'field2' => 'value',
				),
				'model2' => array(
					'field' => 'value',
					'field2' => 'value',
				),
			),
			'data2' => array(
				'model' => array(
					'field' => 'value',
					'field2' => 'value',
				),
				'model2' => array(
					'field' => 'value',
					'fie+ld2' => 'value',
				),
			),
		);
		$query_string = http_build_query($query);
		$get = $this->BaseModule->parse_query($query_string);
		$this->assertEquals($query, $get);
	}
}
