<?php

namespace Modules\Time\Tests\Unit;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Modules\Time\Http\Services\Duration;

class DurationTest extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | Service Class Unit Test #1
    |--------------------------------------------------------------------------
    |
    | Class: Modules\Time\Http\Services\Duration
    | Goal: Assert All inserted Duration formated to HH:MM:SS.
    |
    */

    /** @test */
    public function assert_time_arrangement_fit_time_constraint()
    {
        $x = Carbon::parse('2021-09-09 10:00:00');
        $y = Carbon::parse('2021-09-09 15:30:30');

        $time = new Duration($x, $y);

        $this->assertEquals('05:30:30', $time->inTime());
    }

    /** @test */
    public function assert_diff_in_seconds_equals_start_time_and_end_time_duration()
    {
        $x = Carbon::parse('2021-09-09 10:00:00');
        $y = Carbon::parse('2021-09-09 15:30:30');

        $time = new Duration($x, $y);

        $this->assertEquals(19830, $time->inSecond());
    }

    /** @test */
    public function assert_diff_in_minutes_equals_start_time_and_end_time_duration()
    {
        $x = Carbon::parse('2021-09-09 10:00:00');
        $y = Carbon::parse('2021-09-09 15:30:30');

        $time = new Duration($x, $y);

        $this->assertEquals(330, $time->inMinute());
    }

    /** @test */
    public function assert_diff_in_hours_equals_start_time_and_end_time_duration()
    {
        $x = Carbon::parse('2021-09-09 10:00:00');
        $y = Carbon::parse('2021-09-09 15:30:30');

        $time = new Duration($x, $y);

        $this->assertEquals('5', $time->inHour());
    }
}
