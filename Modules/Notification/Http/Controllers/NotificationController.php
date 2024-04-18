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

    public function updateRead(Request $request)
    {
        $notificationId = $request->query('id');

        $notification = $this->notificationRepository->find($notificationId);

        if ($notification) {
            $notification->update([
                'is_read' => true,
            ]);

            return response()->json([
                'message' => 'Update read sucess',
            ], 200);
        }

        return response()->json([
            'message' => 'No Notification found',
        ], 400);
    }

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
