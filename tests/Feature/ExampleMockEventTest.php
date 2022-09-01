<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\TestExample;
use Illuminate\Support\Facades\Event;

class ExampleMockEventTest extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | Mocking Event on Feature Test
    |--------------------------------------------------------------------------
    |
    | skip Event class so test can check the code after event hit, 
    | Because Event Class usually uses for asyncronous flow like queue, mail, push notification etc.
    | we can uses mock to skip those steps hopefully make the test run more efficiently.
    |
    */

    /** @test */
    public function assert_example_created_was_dispatched()
    {
        $this->withoutExceptionHandling();
        Event::fake();

        $this->postJson(route('api.example.repository.store'), [
            'name' => 'Rendy Anggara',
            'status' => 1
        ])->assertOk();
        
        Event::assertDispatched('example.created');

        $example = TestExample::where('name', 'Rendy Anggara')->first();
        $this->assertNotNull($example);

        $example->delete();
    }

    /** @test */
    public function assert_example_updated_was_dispatched()
    {
        Event::fake();

        $example = TestExample::create([
            'name' => 'Rendy Anggara',
            'status' => 1
        ]);
        
        $this->putJson(route('api.example.repository.update', $example->id), [
            'name' => 'Rendy Anggara UPDATED',
            'status' => 1
        ])->assertOk();
        
        Event::assertDispatched('example.updated');

        $example = TestExample::where('name', 'Rendy Anggara UPDATED')->first();
        $this->assertNotNull($example);

        $example->delete();
    }

    /** @test */
    public function assert_example_deleted_was_dispatched()
    {
        Event::fake();

        $example = TestExample::create([
            'name' => 'Rendy Anggara',
            'status' => 1
        ]);

        $this->deleteJson(route('api.example.repository.delete', $example->id))
            ->assertOk();

        Event::assertDispatched('example.deleted');
        
        $example = TestExample::where('name', 'Rendy Anggara')->first();
        $this->assertNull($example);
    }
}
