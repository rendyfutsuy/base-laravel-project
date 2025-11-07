<?php

namespace Modules\Notification\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Notification\Models\FirebaseToken;

class FirebaseTokenController extends Controller
{
    /**
     * @group Notifications
     *
     * @authenticated
     *
     * Store or update Firebase token for push notifications.
     *
     * @bodyParam token string required The Firebase token. Example: firebase-token-123
     *
     * @response 201 {
     *   "message": "Firebase token stored"
     * }
     * @response 400 {
     *   "message": "Token or device type is not provided"
     * }
     */
    public function store(Request $request)
    {
        $token = $request->input('token');

        if ($token) {
            FirebaseToken::updateOrCreate(
                ['user_id' => auth()->user()->id, 'user_agent' => $request->userAgent()],
                [
                    'token' => $token,
                ]);

            return response()->json([
                'message' => 'Firebase token stored',
            ], 201);
        }

        return response()->json([
            'message' => 'Token or device type is not provided',
        ], 400);
    }

    /**
     * @group Notifications
     *
     * @authenticated
     *
     * Delete Firebase token.
     *
     * @queryParam token string required The Firebase token. Example: firebase-token-123
     *
     * @response 200 {
     *   "message": "Deleted"
     * }
     * @response 400 {
     *   "message": "Error message"
     * }
     */
    public function delete(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            FirebaseToken::where('user_id', '=', auth()->user()->id)
                ->where('token', '=', $request->query('token'))
                ->where('user_agent', '=', $request->userAgent())->delete();

            return response()->json([
                'message' => 'Deleted',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e,
            ], 400
            );
        }
    }
}
