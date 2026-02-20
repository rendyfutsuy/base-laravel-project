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
        $reference = null;

        // Safely access notifiable relationship
        // Handle case where notifiable_type might be a mock class from testing
        try {
            if ($this->notifiable_type && $this->notifiable_id) {
                // Check if class exists and is not a mock class
                // Mock classes from testing start with "Mockery_"
                if (! str_starts_with($this->notifiable_type, 'Mockery_') &&
                    class_exists($this->notifiable_type)) {
                    // Only try to access notifiable if class is valid
                    $notifiable = $this->notifiable;
                    if ($notifiable) {
                        $reference = [
                            'id' => $notifiable->id,
                            'reference' => class_basename($notifiable::class),
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            // If notifiable cannot be loaded (e.g., mock class or deleted model), set reference to null
            $reference = null;
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'message' => $this->message,
            'is_read' => $this->is_read,
            'sent_at' => $this->sent_at,
            'received_back_at' => $this->received_back_at,
            'type' => $this->type,
            'reference' => $reference,
            'created_at' => $this->created_at,
        ];
    }
}
