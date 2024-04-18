<?php

namespace App\Helpers;

trait ApiResponseTrait
{
    /**
     * Render Response in \Illuminate\Http\JsonResponse format
     *
     * @param  mixed  $status
     * @param  mixed  $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function resultResponse($status, string $message, int $code, $data = null)
    {
        $response = [
            'meta' => [
                'status' => $status,
                'message' => $message,
                'status_code' => $code,
            ],
        ];

        if ($data) {
            $response = array_merge($response, [
                'data' => $data,
            ]);
        }

        return response()->json($response, $code);
    }
}
