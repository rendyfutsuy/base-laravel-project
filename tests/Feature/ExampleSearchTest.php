<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\TestExample;

class ExampleSearchTest extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | HttpSearch (Pipeline) Feature Test
    |--------------------------------------------------------------------------
    |
    */

    /** @test */
    public function can_search_with_name()
    {
        $this->withoutExceptionHandling();
        $responses = $this->getJson(route('api.example.index', [
            'search' => 'Luigi',
        ]));
        
        $responses->assertOk();

        $jsonResponses =  $responses->json();
        $this->assertEquals(3, $jsonResponses['meta']['total']);
    }

    /** @test */
    public function can_search_with_status()
    {
        $responses = $this->getJson(route('api.example.index', [
            'status' => 1,
        ]));
        
        $responses->assertOk();

        $jsonResponses =  $responses->json();
        $this->assertEquals(2, $jsonResponses['meta']['total']);
    }

    /** @test */
    public function can_sort_result()
    {
        $responses = $this->getJson(route('api.example.index', [
            'sort_field' => 'test_examples.name',
            'sort_order' => 'ASC',
        ]));
        
        $responses->assertOk();
        $jsonResponses =  $responses->json();
        $this->assertEquals('Adit Saja', $jsonResponses['data']['0']['name']);
    }

    /** @test */
    public function can_sort_result_desc()
    {
        $responses = $this->getJson(route('api.example.index', [
            'sort_field' => 'test_examples.name',
            'sort_order' => 'DESC',
        ]));
        
        $responses->assertOk();
        $jsonResponses =  $responses->json();
        $this->assertEquals('Zanoba Shirone', $jsonResponses['data']['0']['name']);
    }


    /** @test */
    public function if_search_with_unknown_coloumn_will_not_result_error()
    {
        $responses = $this->getJson(route('api.example.index', [
            'status' => 1,
            'sort_field' => 'unknown'
        ]));
        
        $responses->assertOk();
    }
}
