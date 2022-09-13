<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\TestExample;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendExampleChangeNotification;
use App\Mail\SendExampleDeleteNotification;
use App\Mail\SendExampleCreationNotification;

class ExampleMockMailTest extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | Mocking Mail on Feature Test
    |--------------------------------------------------------------------------
    |
    | skip Mail class so test can check the code after Mail hit,
    | we can uses mock to skip this steps hopefully make the test run more efficiently.
    |
    */

    /** @test */
    public function assert_example_send_mail_created_was_dispatched()
    {
        $this->withoutExceptionHandling();
        Mail::fake();

        $this->postJson(route('api.example.repository.store'), [
            'name' => 'Rendy Anggara',
            'status' => 1,
        ])->assertOk();

        Mail::assertSent(SendExampleCreationNotification::class);

        $example = TestExample::where('name', 'Rendy Anggara')->first();
        $this->assertNotNull($example);

        $example->delete();
    }

    /** @test */
    public function assert_example_send_mail_updated_was_dispatched()
    {
        Mail::fake();

        $example = TestExample::create([
            'name' => 'Rendy Anggara',
            'status' => 1,
        ]);

        $this->putJson(route('api.example.repository.update', $example->id), [
            'name' => 'Rendy Anggara UPDATED',
            'status' => 1,
        ])->assertOk();

        Mail::assertSent(SendExampleChangeNotification::class);

        $example = TestExample::where('name', 'Rendy Anggara UPDATED')->first();
        $this->assertNotNull($example);

        $example->delete();
    }

    /** @test */
    public function assert_example_send_mail_deleted_was_dispatched()
    {
        Mail::fake();

        $example = TestExample::create([
            'name' => 'Rendy Anggara',
            'status' => 1,
        ]);

        $this->deleteJson(route('api.example.repository.delete', $example->id))
            ->assertOk();

        Mail::assertSent(SendExampleDeleteNotification::class);

        $example = TestExample::where('name', 'Rendy Anggara')->first();
        $this->assertNull($example);
    }
}
