<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class Example3rdParty
{
    public function fetch()
    {
        $response = Http::acceptJson()->get(config('example.3rd_party_url'));
        $body = json_decode($response->getBody(), 1);
        $status = $response->status();

        if ($status != 200) {
            return [
                'status' => 400,
                'message' => 'something went wrong',
                'body' => $body,
            ];
        }

        return [
            'status' => 200,
            'message' => 'success',
            'body' => $body,
        ];
    }

    public function store($attributes = [])
    {
        $response = Http::acceptJson()->post(config('example.3rd_party_url'), $attributes);
        $body = json_decode($response->getBody(), 1);
        $status = $response->status();

        if ($status != 200) {
            return [
                'status' => 400,
                'message' => 'something went wrong',
                'body' => $body,
            ];
        }

        return [
            'status' => 201,
            'message' => 'success',
            'body' => $body,
        ];
    }
}
