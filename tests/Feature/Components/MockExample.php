<?php

namespace Tests\Feature\Components;

use Illuminate\Support\Facades\Http;

trait MockExample
{
    protected function mockFetchApi()
    {
        Http::fake([
            config('example.3rd_party_url') => Http::response([
                'status' => [
                    'code' => '200',
                    'message' => 'Data Fetched',
                ],
                'meta' => [],
                'data' => [],
            ], 200),
        ]);
    }

    protected function mockFetchApiFail()
    {
        Http::fake([
            config('example.3rd_party_url') => Http::response([
                'status' => [
                    'code' => '400',
                    'message' => 'FAILED TO LOAD DATA',
                ],
            ], 400),
        ]);
    }

    protected function mockPostApi()
    {
        Http::fake([
            config('example.3rd_party_url') => Http::response([
                'status' => [
                    'code' => '201',
                    'message' => 'DATA VALIDATED',
                ],
                'name' => 'Rendy',
                'level' => 'Captain',
                'validated' => true,
            ], 200),
        ]);
    }

    protected function mockPostApiFail()
    {
        Http::fake([
            config('example.3rd_party_url') => Http::response([
                'status' => [
                    'code' => '500',
                    'message' => 'Something went wrong!',
                ],
            ], 500),
        ]);
    }
}
