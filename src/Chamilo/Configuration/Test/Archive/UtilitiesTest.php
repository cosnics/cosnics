<?php
namespace Chamilo\Configuration\Test\Archive;

class UtilitiesTest extends \PHPUnit_Framework_TestCase
{

    // normal unit test cases
    public function test_underscores_to_camelcase_normal()
    {
        $s = (string) StringUtilities :: getInstance()->createString('abc_def')->upperCamelize();
        $this->assertTrue($s === 'AbcDef');
    }

    public function test_format_seconds_to_hours_normal()
    {
        $s = DatetimeUtilities :: format_seconds_to_hours('3600');
        $this->assertTrue($s === '1:00:00');
    }

    public function test_format_seconds_to_minutes_normal()
    {
        $s = DatetimeUtilities :: format_seconds_to_minutes('60');
        $this->assertTrue($s === '01:00');
    }

    // null unit test cases
    public function test_format_seconds_to_hours_null()
    {
        $s = DatetimeUtilities :: format_seconds_to_hours(null);
        $this->assertEquals('0:00:00', $s);
    }

    public function test_format_seconds_to_minutes_null()
    {
        $s = DatetimeUtilities :: format_seconds_to_minutes(null);
        $this->assertEquals('00:00', $s);
    }

    // isnotnull unit test cases
    public function test_format_seconds_to_hours_not_null()
    {
        $s = DatetimeUtilities :: format_seconds_to_hours('3600');
        $this->assertNotNull($s === '1:00:00');
    }

    public function test_format_seconds_to_minutes_not_null()
    {
        $s = DatetimeUtilities :: format_seconds_to_minutes('60');
        $this->assertNotNull($s === '01:00');
    }

    public function test_truncate_string()
    {
        $s = StringUtilities :: getInstance()->truncate('Testing the utilities functions', 10);
        $this->assertTrue($s === "Testing t\xE2\x80\xA6");
    }
}
