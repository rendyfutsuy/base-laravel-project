<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tests\Feature\Components\MockExample;
use App\Http\Services\Example3rdParty as Service;

class Example3rdParty extends TestCase
{
    use MockExample;

    /*
    |--------------------------------------------------------------------------
    | Http Class Unit Test #1
    |--------------------------------------------------------------------------
    |
    | Class: App\Http\Services\Example3rdParty
    | Goal: Assert all method in class can be mocked. and return result based on initiated result.
    |
    */

    /** @test */
    public function mock_fetch_from_rest_api_success()
    {
        $this->mockFetchApi();
        $service = new Service();
        $response = $service->fetch();

        $this->assertEquals(200, $response['status']);
    }

    /** @test */
    public function mock_post_to_rest_api_success()
    {
        $this->mockPostApi();
        $service = new Service();
        $response = $service->store([
            'name' => 'rendy'
        ]);

        $this->assertEquals(200, $response['status']);
    }

    /** @test */
    public function mock_fetch_from_rest_api_fail()
    {
        $this->mockFetchApiFail();
        $service = new Service();
        $response = $service->fetch();
        
        $this->assertEquals(400, $response['status']);
    }

    /** @test */
    public function mock_post_to_rest_api_fail()
    {
        $this->mockPostApiFail();
        $service = new Service();
        $response = $service->store([
            'name' => 'rendy'
        ]);
        
        $this->assertEquals(400, $response['status']);
    }
}
