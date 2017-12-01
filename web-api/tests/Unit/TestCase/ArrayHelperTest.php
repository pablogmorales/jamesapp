<?php
namespace Daytalytics\Tests\Unit\TestCase;

use PHPUnit\Framework\TestCase;
use Daytalytics\ArrayHelper;

/**
 *
 * @coversDefaultClass Daytalytics\ArrayHelper
 *
 */
class ArrayHelperTest extends TestCase
{

    /**
     * @test
     * @covers ::filter_fields
     */
    public function filter_fields()
    {
        $input = [[
            'fieldNameOne' => 'value one',
            'fieldNameTwo' => 'value two',
            'fieldNameThree' => 'value three',
            'fieldNameFour' => 'value four',
        ]];
        
        $expected = [[
            'fieldNameOne' => 'value one',
            'fieldNameFour' => 'value four'
        ]];
        
        $actual = ArrayHelper::filter_fields($input, 'defaults', [
            'fieldNameOne',
            'fieldNameFour'
        ]);
        $this->assertEquals($expected, $actual);
    }
}