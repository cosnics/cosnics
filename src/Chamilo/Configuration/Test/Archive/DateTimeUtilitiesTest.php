<?php
namespace Chamilo\Configuration\Test\Archive;

use PHPUnit_Framework_TestCase;

class DateTimeUtilitiesTest extends PHPUnit_Framework_TestCase
{

    public function setUp(): void
    {
    }

    public function test_format_locale_date_sould_produce_an_english_date()
    {
        $timestamp = \Chamilo\Mktime(14, 36, 02, 02, 25, 2010);
        $date_format = "%B %d, %Y at %I:%M %p";
        $returnValue = DatetimeUtilities::format_locale_date($date_format, $timestamp);
        $this->assertEquals("February 25, 2010 at 02:36 PM", $returnValue);
    }

    public function test_format_locale_date_replace_aAbB_keywords_by_wwmm()
    {
        $timestamp = \Chamilo\Mktime(14, 36, 02, 02, 25, 2010);
        $returnValue = DatetimeUtilities::format_locale_date("%a%A%b%B", $timestamp);
        $this->assertEquals("ThThursdayFebFebruary", $returnValue);
    }

    public function test_convert_seconds_to_hours()
    {
        // negative value are not converted
        $this->assertEquals("-1s", DatetimeUtilities::convert_seconds_to_hours(- 1));
        $this->assertEquals("-59s", DatetimeUtilities::convert_seconds_to_hours(- 59));
        $this->assertEquals("-61s", DatetimeUtilities::convert_seconds_to_hours(- 61));
        $this->assertEquals("1s", DatetimeUtilities::convert_seconds_to_hours(01));
        $this->assertEquals("59s", DatetimeUtilities::convert_seconds_to_hours(59));
        $this->assertEquals("1m 0s", DatetimeUtilities::convert_seconds_to_hours(60));
        $this->assertEquals("59m 59s", DatetimeUtilities::convert_seconds_to_hours(3599));
        $this->assertEquals("1h 0m 0s", DatetimeUtilities::convert_seconds_to_hours(3600));
        $this->assertEquals("23h 59m 59s", DatetimeUtilities::convert_seconds_to_hours(86399));
        $this->assertEquals("24h 0m 0s", DatetimeUtilities::convert_seconds_to_hours(86400));
    }
}
