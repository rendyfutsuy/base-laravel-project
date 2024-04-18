<?php

namespace Modules\Notification\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'message' => $this->message,
            'is_read' => $this->is_read,
            'sent_at' => $this->sent_at,
            'received_back_at' => $this->received_back_at,
            'type' => $this->type,
            'reference' => $this->notifiable ? [
                'id' => $this->notifiable->id,
                'reference' => class_basename($this->notifiable::class),
            ] : null,
            'created_at' => $this->created_at,
        ];
    }
}
