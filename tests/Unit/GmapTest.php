<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Location\Gmap;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class GmapTest extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | Http Class Unit Test #2
    |--------------------------------------------------------------------------
    |
    | Class: App\Location\Gmap
    | Goal: Assert all method in class can be mocked. and return result based on initiated result.
    |
    */

    /** @test */
    public function gmap_key_must_set()
    {
        config('app.gmap_key', null);
        $gmap = new Gmap();
        $location = $gmap->find('-6.8998024,107.5215078');

        $this->assertEquals($location['status'], 'fail');

        config('app.gmap_key', 'itsjustwork');
    }

    /** @test */
    public function gmap_can_be_use_to_get_location_base_on_coordinate()
    {
        $this->mockLocation();

        $gmap = new Gmap();
        $location = $gmap->find('-6.8998024,107.5215078');

        $this->assertEquals(
            $location,
            'Jalan Mutiara, Sukamenak, Margahayu, Bandung, West Java 40239, Indonesia'
        );
    }

    /** @test */
    public function gmap_can_not_be_use_if_coordinate_not_filled()
    {
        $gmap = new Gmap();
        $location = $gmap->find(null);

        $this->assertEquals('fail', $location['status']);
    }

    /** @test */
    public function gmap_can_not_be_use_if_gmap_secret_key_is_null()
    {
        $previousKey = Config::get('app.gmap_key');
        Config::set('app.gmap_key', null);

        $gmap = new Gmap();
        $location = $gmap->find('-6.8998024,107.5215078');

        $this->assertEquals('fail', $location['status']);

        Config::set('app.gmap_key', $previousKey);
    }

    /** @test */
    public function if_coordinate_not_valid_gmap_will_fail()
    {
        $this->mockLocationToFail();

        $gmap = new Gmap();
        $location = $gmap->find('-6.8998024');

        $this->assertEquals('fail', $location['status']);
    }

    protected function mockLocation(): void
    {
        Http::fake(function () {
            // Stub a JSON response for Gmap endpoints...
            return Http::response([
                'results' => [
                    [
                        'formatted_address' => 'Jalan Mutiara, Sukamenak, Margahayu, Bandung, West Java 40239, Indonesia',
                    ],
                ],
            ]);
        });
    }

    protected function mockLocationToFail(): void
    {
        Http::fake(function () {
            // Stub a JSON response for Gmap endpoints...
            return Http::response([
                'results' => [],
                'error_message' => 'Invalid Coordinate',
            ]);
        });
    }
}
