<?php

namespace App\Http\Services\Traits;

trait ResultResponse
{
    public function resultResponse($status, $message, $code)
    {
        return response()->json(['status' => $status, 'message' => $message], $code);
    }

    public function response(string $status, $data = null, $message = null, $code = 200)
    {
        $response = [];
        $response['status'] = $status;

        if (null !== $message) {
            $response['message'] = $message;
        }

        if (null !== $data) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }
}
