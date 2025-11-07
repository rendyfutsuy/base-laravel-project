<?php

namespace Modules\Notification\Http\Repositories;

use App\Http\Repositories\BaseRepository;
use Modules\Notification\Models\Notification;
use Modules\Notification\Http\Repositories\Contracts\NotificationContract;

class NotificationRepository extends BaseRepository implements NotificationContract
{
    protected $model;

    public function __construct(Notification $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function countUnread()
    {
        $userId = auth()->user()->id;

        return $this->model
            ->where('user_id', $userId)
            ->unread()
            ->count();
    }

    public function find($id): \Illuminate\Database\Eloquent\Model
    {
        return $this->model->findOrFail($id);
    }

    public function updateRead($id): bool
    {
        try {
            $notification = $this->find($id);

            return $notification->update([
                'is_read' => true,
            ]);
        } catch (\Exception $e) {
            return false;
        }
    }
}
