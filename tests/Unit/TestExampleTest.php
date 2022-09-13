<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\TestExample;
use PHPUnit\Framework\Test;
use Illuminate\Database\Eloquent\Model;

class TestExampleTest extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | Model Unit Test
    |--------------------------------------------------------------------------
    |
    | Class: App\Models\TestExample
    | Goal: Assert all mutators, casting, eloquent relations work the way it should be.
    |
    */

    /** @test  */
    public function test_example_model_is_extension_from_model_class()
    {
        $example = new TestExample();

        $this->assertTrue($example instanceof Model);
    }

    /** @test  */
    public function status_text_attribute_can_append_text_to_indicate_status()
    {
        $example = TestExample::find(1);
        $this->assertEquals(__('status.active'), $example->status_text);

        $example = TestExample::find(2);
        $this->assertEquals(__('status.expired'), $example->status_text);

        $example = TestExample::find(3);
        $this->assertEquals(__('status.rejected'), $example->status_text);

        $example = TestExample::find(7);
        $this->assertEquals(__('status.pending'), $example->status_text);
    }

    /** @test  */
    public function joined_at_format_is_overidden_by_mutator()
    {
        $example = TestExample::find(1);
        $this->assertEquals('2021/09/09 10:00:00', $example->joined_at);
        $this->assertTrue($example->joined_timestamp instanceof Carbon);
    }

    /** @test  */
    public function when_creating_new_example_status_and_joined_at_automatically_filled()
    {
        $knownDate = Carbon::parse('2021-09-09 10:00:00');
        Carbon::setTestNow($knownDate);

        $example = TestExample::create([
            'name' => 'Rendy',
        ]);

        $this->assertEquals('2021/09/09 10:00:00', $example->joined_at);
        $this->assertEquals(TestExample::PENDING, $example->status);

        $example->delete();
    }

    /** @test  */
    public function when_creating_new_example_status_and_joined_at_can_be_manually_filled()
    {
        $knownDate = Carbon::parse('2021-10-10 10:00:00');

        $example = TestExample::create([
            'name' => 'Rendy',
            'joined_at' => $knownDate,
            'status' => TestExample::EXPIRED,
        ]);

        $this->assertEquals('2021/10/10 10:00:00', $example->joined_at);
        $this->assertEquals(TestExample::EXPIRED, $example->status);

        $example->delete();
    }

    /** @test  */
    public function when_updating_example_status_automatically_status_can_be_null()
    {
        $example = TestExample::create([
            'name' => 'Rendy',
        ]);

        TestExample::find($example->id)->update([
            'name' => 'Rendy UPDATED',
            'status' => null,
        ]);

        $this->assertEquals(null, $example->fresh()->status);

        $example->delete();
    }
}
