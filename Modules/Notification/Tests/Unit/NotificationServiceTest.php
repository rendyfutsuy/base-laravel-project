<?php

namespace Modules\Notification\Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Modules\Hierarchy\Models\Role;
use Modules\Notification\Models\Notification;
use Modules\Notification\Services\Firebase\FCM;
use Modules\Notification\Http\Services\Features\NotificationService;

class NotificationServiceTest extends TestCase
{
    /** @test */
    public function can_create_notification(): void
    {
        $service = app()->make(NotificationService::class);
        $role = Role::first();
        $receiver = User::first();

        $service->add(
            'TRIAL',
            'Trial',
            'trial',
            $role,
            $receiver, // Receiver
            $role->toArray()
        );

        $notification = Notification::where('type', 'trial')->first();
        $this->assertNotEmpty($notification);
    }

    /** @test */
    public function can_create_notification_with_error_log_type(): void
    {
        $service = app()->make(NotificationService::class);
        $receiver = User::first();

        $service->addErrorLog(
            $receiver,
            'Customer Sync Fail...',
            'Something happen when Create customer data. Please check your console.',
            [
                'error' => 'FAIL...',
                'data' => [],
            ]
        );

        $notification = Notification::where('type', 'error_log')->first();
        $this->assertNotEmpty($notification);
    }

    /** @test */
    public function can_create_user_created_notification(): void
    {
        $service = app()->make(NotificationService::class);
        $user = User::first();
        $receiver = User::first();

        $service->userCreated(
            $user,
            $receiver, // Receiver
        );

        $notification = Notification::where('type', 'user_created')->first();
        $this->assertNotEmpty($notification);
    }

    /** @test */
    public function can_create_user_updated_notification(): void
    {
        $service = app()->make(NotificationService::class);
        $user = User::first();
        $receiver = User::first();

        $service->userUpdated(
            $user,
            $receiver, // Receiver
            $user->toArray(), // before edit
            [
                'name' => 'Changed Name',
                'email' => 'changed.email@mailinator.com',
            ] // after edit
        );

        $notification = Notification::where('type', 'user_updated')->first();
        $this->assertNotEmpty($notification);
    }

    /** @test */
    public function can_create_user_deleted_notification(): void
    {
        $service = app()->make(NotificationService::class);
        $user = User::first();
        $receiver = User::first();

        $service->userDeleted(
            $user,
            $receiver, // Receiver
        );

        $notification = Notification::where('type', 'user_deleted')->first();
        $this->assertNotEmpty($notification);
    }

    /** @test */
    public function can_send_notification_with_push_notification(): void
    {
        $this->mock(FCM::class, function ($mock) {
            $mock->shouldReceive('sendMulti')
                ->andReturn(true);
        });

        $service = app()->make(NotificationService::class);
        $notification = Notification::first();
        $receiver = User::first();

        $service->sendPushNotification($receiver, $notification);

        $notification = Notification::where('id', $notification->id)->first();
        $this->assertNotEmpty($notification);
        $this->assertNotNull($notification->pushed_at);
    }
}
