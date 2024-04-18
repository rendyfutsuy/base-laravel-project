<?php

namespace Modules\Location\Http\Services;

class Radius
{
    /** find between place currently in and the destination
     * example parameter structure for $params
     *
     * $params['target_coordinates'] = [
     *      'latitude' => '-6.9560321',
     *      'longitude' => '107.8441451',
     *  ];
     *
     *  $params['dynamic_coordinates'] = [
     *      'latitude' => '-6.9560331',
     *      'longitude' => '107.8441441',
     *  ];
     */
    public function distance(array $params)
    {
        $validation = $this->validateCoordinatesRequest($params);
        if (! $validation['status']) {
            return ['status' => false, 'message' => $validation['message']];
        }

        $targetCo = $params['target_coordinates'];
        $dynamicCo = $params['dynamic_coordinates'];
        // convert from degrees to radians
        $lat1 = deg2rad($dynamicCo['latitude']);
        $lon1 = deg2rad($dynamicCo['longitude']);
        $lat2 = deg2rad($targetCo['latitude']);
        $lon2 = deg2rad($targetCo['longitude']);

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) *
            cos(deg2rad($lat2)) * cos(deg2rad($theta));

        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;

        return ($miles * 1.609344) * 10000; // covert miles to meter
    }

    // Validate Coordination params
    public function validateCoordinatesRequest(array $params)
    {
        if (! empty($params)) {
            $return = 0;
            $exist = ['target_coordinates', 'dynamic_coordinates', 'latitude', 'longitude'];

            foreach ($params as $key => $value) {
                if ($key == 'target_coordinates') {
                    if (! empty($value)) {
                        foreach ($value as $key => $value) {
                            if ($value == null) {
                                $value = '';
                            }
                            if ($key == 'latitude') {
                                if ($value !== '') {
                                    $return++;
                                }
                            }
                            if ($key == 'longitude') {
                                if ($value !== '') {
                                    $return++;
                                }
                            }
                            if (! in_array($key, $exist, true)) {
                                return [
                                    'status' => false,
                                    'message' => 'Unable to process, you must input
                                                  array latitude and longitude not exist {'.$key.'}',
                                ];
                            }
                        }
                    }
                }
                if ($key == 'dynamic_coordinates') {
                    if (! empty($value)) {
                        foreach ($value as $key => $value) {
                            if ($value == null) {
                                $value = '';
                            }
                            if ($key == 'latitude') {
                                if ($value !== '') {
                                    $return++;
                                }
                            }
                            if ($key == 'longitude') {
                                if ($value !== '') {
                                    $return++;
                                }
                            }
                            if (! in_array($key, $exist, true)) {
                                return [
                                    'status' => false,
                                    'message' => 'Unable to process, you must input
                                                  array latitude and longitude not exist {'.$key.'}',
                                ];
                            }
                        }
                    }
                }
                if (! in_array($key, $exist, true)) {
                    return [
                        'status' => false,
                        'message' => 'Unable to process,
                                      you must input array target_coordinates and dynamic_coordinates',
                    ];
                }
            }
            if ($return !== 4) {
                return [
                    'status' => false,
                    'message' => 'Unable to process, you must input array latitude and longitude',
                ];
            }

            return [
                'status' => true,
                'message' => 'Valid Radius',
            ];
        } else {
            return [
                'status' => false,
                'message' => 'Unable to process, sorry your coordinates are not complete',
            ];
        }
    }
}
