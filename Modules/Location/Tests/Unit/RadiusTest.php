<?php

namespace Modules\Location\Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Modules\Location\Http\Services\Radius;

class RadiusTest extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | Http Class Unit Test #2
    |--------------------------------------------------------------------------
    |
    | Class: Modules\Location\Http\Services\Radius
    | Goal: Assert all method in class can be mocked. and return result based on initiated result.
    |
    */

    /** @test */
    public function assert_distance_return_int_if_parameters_valid()
    {
        $service = new Radius;

        $params['target_coordinates'] = [
            'latitude' => '-6.9560321',
            'longitude' => '107.8441451',
        ];
        $params['dynamic_coordinates'] = [
            'latitude' => '-6.9560331',
            'longitude' => '107.8441441',
        ];

        $response = $service->distance($params);

        $this->assertIsNotArray($response);
        $this->assertIsNumeric($response);
        $this->assertEquals(0.0, $response);
    }

    /** @test */
    public function assert_distance_return_array_if_latitude__target_parameters_is_not_valid()
    {
        $service = new Radius;

        $params['target_coordinates'] = [
            'latituder' => '-6.9560321',
            'longitude' => '107.8441451',
        ];
        $params['dynamic_coordinates'] = [
            'latitude' => '-6.9560331',
            'longitude' => '107.8441441',
        ];

        $response = $service->distance($params);

        $this->assertIsArray($response);
        $this->assertIsNotNumeric($response);
    }

    /** @test */
    public function assert_distance_return_array_if_longitude__target_parameters_is_not_valid()
    {
        $service = new Radius;

        $params['target_coordinates'] = [
            'latitude' => '-6.9560321',
            'longituder' => '107.8441451',
        ];
        $params['dynamic_coordinates'] = [
            'latitude' => '-6.9560331',
            'longitude' => '107.8441441',
        ];

        $response = $service->distance($params);

        $this->assertIsArray($response);
        $this->assertIsNotNumeric($response);
    }

    /** @test */
    public function assert_distance_return_array_if_latitude__dynamic_parameters_is_not_valid()
    {
        $service = new Radius;

        $params['target_coordinates'] = [
            'latitude' => '-6.9560321',
            'longitude' => '107.8441451',
        ];
        $params['dynamic_coordinates'] = [
            'latituder' => '-6.9560331',
            'longitude' => '107.8441441',
        ];

        $response = $service->distance($params);

        $this->assertIsArray($response);
        $this->assertIsNotNumeric($response);
    }

    /** @test */
    public function assert_distance_return_array_if_longitude__dynamic_parameters_is_not_valid()
    {
        $service = new Radius;

        $params['target_coordinates'] = [
            'latitude' => '-6.9560321',
            'longitude' => '107.8441451',
        ];
        $params['dynamic_coordinates'] = [
            'latitude' => '-6.9560331',
            'longituder' => '107.8441441',
        ];

        $response = $service->distance($params);

        $this->assertIsArray($response);
        $this->assertIsNotNumeric($response);
    }
}
