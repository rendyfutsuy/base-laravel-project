<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\TestExample;
use Illuminate\Support\Facades\Event;

class ExampleRepositoryTest extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | Repository Feature Test
    |--------------------------------------------------------------------------
    |
    */

    /** @test */
    public function can_get_example_list_with_example_repository()
    {
        $this->withoutExceptionHandling();
        $response = $this->getJson(route('api.example.repository.index'));

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'name',
                    'status',
                    'is_example',
                ],
            ],
        ]);

        $this->assertEquals(7, count($response->json()['data']));
    }

    /** @test */
    public function can_create_example_with_example_repository()
    {
        $this->withoutExceptionHandling();
        Event::fake();

        $this->postJson(route('api.example.repository.store'), [
            'name' => 'Rendy Anggara',
            'status' => 1,
        ])->assertOk();

        $example = TestExample::where('name', 'Rendy Anggara')->first();
        $this->assertNotNull($example);

        $example->delete();
    }

    /** @test */
    public function can_update_example_with_example_repository()
    {
        Event::fake();

        $example = TestExample::create([
            'name' => 'Rendy Anggara',
            'status' => 1,
        ]);

        $this->putJson(route('api.example.repository.update', $example->id), [
            'name' => 'Rendy Anggara UPDATED',
            'status' => 99,
        ])->assertOk();

        $example = TestExample::where('name', 'Rendy Anggara UPDATED')->first();
        $this->assertEquals('Rendy Anggara UPDATED', $example->name);
        $this->assertEquals(99, $example->status);

        $example->delete();
    }

    /** @test */
    public function can_delete_example_with_example_repository()
    {
        Event::fake();

        $example = TestExample::create([
            'name' => 'Rendy Anggara',
            'status' => 1,
        ]);

        $this->deleteJson(route('api.example.repository.delete', $example->id))
            ->assertOk();

        $example = TestExample::where('name', 'Rendy Anggara')->first();
        $this->assertNull($example);
    }
}
