<?php

namespace Modules\Notification\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Notification\Http\Resources\NotificationResource;
use Modules\Notification\Http\Services\Searches\NotificationSearch;
use Modules\Notification\Http\Repositories\Contracts\NotificationContract;

class NotificationController extends Controller
{
    public function __construct(NotificationContract $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    protected function search()
    {
        return app()->make(NotificationSearch::class)
            ->assignUser(auth()->user())
            ->apply();
    }

    /**
     * @group Notifications
     * @authenticated
     * 
     * Get paginated list of notifications for authenticated user.
     * 
     * @queryParam row integer Items per page. Example: 10
     * @queryParam page integer Page number. Example: 1
     * 
     * @response 200 {
     *   "data": [
     *     {
     *       "id": "notification-id-123",
     *       "user_id": "user-id-123",
     *       "title": "New Notification",
     *       "message": "You have a new notification",
     *       "type": "info",
     *       "is_read": false,
     *       "sent_at": "2024-01-01T00:00:00.000000Z",
     *       "received_back_at": null,
     *       "payload": null
     *     }
     *   ],
     *   "links": {...},
     *   "meta": {...},
     *   "unread_counter": 5
     * }
     */
    public function index(Request $request)
    {
        $row = $request->query('row', 10);

        $unreadNotifications = $this->notificationRepository->countUnread();

        $notifications = $this->search()->paginate($row);

        return NotificationResource::collection($notifications)
            ->additional([
                'unread_counter' => $unreadNotifications,
            ]);
    }

    /**
     * @group Notifications
     * @authenticated
     * 
     * Mark a notification as read.
     * 
     * @queryParam id string required The notification ID. Example: notification-id-123
     * 
     * @response 200 {
     *   "message": "Update read sucess"
     * }
     * @response 400 {
     *   "message": "No Notification found"
     * }
     */
    public function updateRead(Request $request)
    {
        $notificationId = $request->query('id');

        $updated = $this->notificationRepository->updateRead($notificationId);

        if ($updated) {
            return response()->json([
                'message' => 'Update read sucess',
            ], 200);
        }

        return response()->json([
            'message' => 'No Notification found',
        ], 400);
    }

    /**
     * @group Notifications
     * @authenticated
     * 
     * Delete a notification.
     * 
     * @queryParam id string required The notification ID. Example: notification-id-123
     * 
     * @response 200 {
     *   "message": "Notification deleted"
     * }
     * @response 400 {
     *   "message": "Error message"
     * }
     */
    public function delete(Request $request)
    {
        $notificationId = $request->query('id');

        try {
            $this->notificationRepository->find($notificationId)->delete();

            return response()->json([
                'message' => 'Notification deleted',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e,
            ], 400);
        }
    }
}
