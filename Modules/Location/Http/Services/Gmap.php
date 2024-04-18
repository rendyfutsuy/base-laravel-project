<?php

namespace Modules\Location\Http\Services;

use Illuminate\Support\Facades\Http;

class Gmap
{
    /**
     * find Location based on latitude and longitude given by coordinate
     *
     * @param  ?string  $coordinate
     * @return string
     */
    public function find($coordinate = null)
    {
        $key = config('location.gmap_key');

        if (! $key) {
            return [
                'status' => 'fail',
                'message' => 'Google Map Key is Null',
            ];
        }

        if (! $coordinate) {
            return [
                'status' => 'fail',
                'message' => 'Coordinate is null',
            ];
        }

        $address = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'latlng' => $coordinate,
            'key' => $key,
        ]);

        $res = json_decode($address->getBody(), true);

        if (empty($res['results'])) {
            return [
                'status' => 'fail',
                'message' => $res['error_message'],
            ];
        }

        return $res['results'][0]['formatted_address'];
    }
}
