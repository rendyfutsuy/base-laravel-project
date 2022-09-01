<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Feature\Components\MockExample;

class ExampleMock3rdPartyTest extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | Mocking Http Class on Feature Test
    |--------------------------------------------------------------------------
    |
    | mock Http Class so test can skip 3rd party API Request, 
    | and check the condition based on API response without even really hit Their API.
    | we can uses mock to skip those steps hopefully make the test run more efficiently.
    |
    */

    use MockExample;
    
    /** @test */
    public function mock_fetch_from_rest_api_success()
    {
        $this->mockFetchApi();
        $response = $this->get(route('api.example.index.3rd'));

        $response->assertStatus(200);
    }

    /** @test */
    public function mock_post_to_rest_api_success()
    {
        $this->mockPostApi();
        $response = $this->post(route('api.example.post.3rd'), [
            'name' => "rendy",
            'level' => "Captain",
        ]);
        
        $response->assertStatus(201);
    }

    /** @test */
    public function mock_fetch_from_rest_api_fail()
    {
        $this->mockFetchApiFail();
        $response = $this->get(route('api.example.index.3rd'));
        
        $response->assertStatus(400);
    }

    /** @test */
    public function mock_post_to_rest_api_fail()
    {
        $this->mockPostApiFail();
        $response = $this->post(route('api.example.post.3rd'), [
            'name' => "rendy",
            'level' => "Captain",
        ]);

        $response->assertStatus(400);
    }
}
