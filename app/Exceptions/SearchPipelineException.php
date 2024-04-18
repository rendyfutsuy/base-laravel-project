<?php

namespace App\Exceptions;

use Exception;

class SearchPipelineException extends Exception
{
    public $message = 'Some Error';

    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function render()
    {
        return response()->json([
            'message' => $this->message,
        ], 400);
    }
}
