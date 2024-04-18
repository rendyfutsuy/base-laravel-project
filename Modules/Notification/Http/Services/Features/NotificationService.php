<?php

namespace Modules\Notification\Http\Services\Features;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Modules\Notification\Models\Notification;
use Modules\Notification\Services\Firebase\FCM;
use Modules\Notification\Http\Resources\NotificationResource;
use Modules\Notification\Http\Services\Searches\NotificationSearch;
use Modules\Notification\Http\Services\Features\Traits\RoleModuleTrait;
use Modules\Notification\Http\Services\Features\Traits\UserModuleTrait;

class NotificationService
{
    use RoleModuleTrait, UserModuleTrait;

    public $fcm;

    public function __construct(FCM $fcm)
    {
        $this->fcm = $fcm;
    }

    public function add(
        $title,
        $message,
        $type,
        ?Model $model,
        User $receiver,
        $payload = null
    ) {
        $params = [
            'user_id' => $receiver->id,
            'title' => $title,
            'message' => $message,
            'sent_at' => null,
            'received_back_at' => null,
            'is_read' => false,
            'payload' => $payload,
            'type' => $type,
        ];

        if ($model) {
            $params['notifiable_type'] = $model::class;
            $params['notifiable_id'] = $model->id;
        }

        $notification = Notification::create($params);

        return $notification;
    }

    public function addErrorLog(
        User $user,
        $title,
        $message,
        $payload = null
    ) {
        $type = 'error_log';

        return $this->add(
            $title,
            $message,
            $type,
            null,
            $user,
            $payload
        );
    }

    public function index(array $params)
    {
        $notification = app()->make(NotificationSearch::class)
            ->apply($params);

        return NotificationResource::collection($notification->paginate(request('per_page') ?? 10));
    }

    public function read($id = null)
    {
        Notification::when($id, function ($query) use ($id) {
            return $query->where('id', $id);
        })->update([
            'is_read' => true,
        ]);
    }

    public function findService($id)
    {
        $notification = Notification::query()
            ->where('id', $id)
            ->where('user_id', auth()->user()->id)
            ->firstOrFail();

        return new NotificationResource($notification);
    }

    public function sendPushNotification(User $user, Notification $notification): void
    {
        $tokens = $user->firebaseTokens->pluck('token')->toArray();
        $body = [
            'title' => $notification->title,
            'body' => $notification->message,
        ];

        try {
            $this->fcm->sendMulti($tokens, $body, $notification->payload);
            $notification->pushed_at = Carbon::now();
            $notification->save();

        } catch (\Throwable $th) {
            $notification->pushed_at = null;
            $notification->save();

            throw $th;
        }
    }
}
