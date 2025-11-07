<?php

namespace Modules\Notification\Tests\Unit;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use Modules\Hierarchy\Models\Role;
use Modules\Notification\Models\Notification;
use Modules\Notification\Services\Firebase\FCM;
use Modules\Notification\Http\Services\Features\NotificationService;

class NotificationServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function can_create_notification(): void
    {
        $fcmMock = Mockery::mock(FCM::class);

        $notificationModelMock = Mockery::mock(Notification::class)->makePartial();
        $notificationModelMock->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($params) {
                return $params['user_id'] === 'user-id-123'
                    && $params['title'] === 'TRIAL'
                    && $params['message'] === 'Trial'
                    && $params['type'] === 'trial';
            }))
            ->andReturn($notificationModelMock);

        $service = new NotificationService($fcmMock, $notificationModelMock);

        $role = Mockery::mock(Role::class)->makePartial();
        $role->id = 'role-id-123';
        $role->shouldReceive('toArray')->andReturn(['id' => 'role-id-123', 'name' => 'Test Role']);

        $receiver = Mockery::mock(User::class)->makePartial();
        $receiver->id = 'user-id-123';

        $result = $service->add(
            'TRIAL',
            'Trial',
            'trial',
            $role,
            $receiver,
            $role->toArray()
        );

        $this->assertInstanceOf(Notification::class, $result);
    }

    /** @test */
    public function can_create_notification_with_error_log_type(): void
    {
        $fcmMock = Mockery::mock(FCM::class);

        $notificationModelMock = Mockery::mock(Notification::class)->makePartial();
        $notificationModelMock->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($params) {
                return $params['user_id'] === 'user-id-123'
                    && $params['type'] === 'error_log';
            }))
            ->andReturn($notificationModelMock);

        $service = new NotificationService($fcmMock, $notificationModelMock);

        $receiver = Mockery::mock(User::class)->makePartial();
        $receiver->id = 'user-id-123';

        $service->addErrorLog(
            $receiver,
            'Customer Sync Fail...',
            'Something happen when Create customer data. Please check your console.',
            [
                'error' => 'FAIL...',
                'data' => [],
            ]
        );

        $this->assertTrue(true);
    }

    /** @test */
    public function can_create_user_created_notification(): void
    {
        $fcmMock = Mockery::mock(FCM::class);

        $notificationModelMock = Mockery::mock(Notification::class)->makePartial();
        $notificationModelMock->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($params) {
                return $params['type'] === 'user_created';
            }))
            ->andReturn($notificationModelMock);

        $service = new NotificationService($fcmMock, $notificationModelMock);

        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('getAttribute')->with('name')->andReturn('Test User');
        $user->shouldReceive('toArray')->andReturn(['id' => 'user-id-123', 'name' => 'Test User']);

        $receiver = Mockery::mock(User::class)->makePartial();
        $receiver->id = 'receiver-id-123';

        $service->userCreated($user, $receiver);

        $this->assertTrue(true);
    }

    /** @test */
    public function can_create_user_updated_notification(): void
    {
        $fcmMock = Mockery::mock(FCM::class);

        $notificationModelMock = Mockery::mock(Notification::class)->makePartial();
        $notificationModelMock->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($params) {
                return $params['type'] === 'user_updated';
            }))
            ->andReturn($notificationModelMock);

        $service = new NotificationService($fcmMock, $notificationModelMock);

        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('getAttribute')->with('name')->andReturn('Test User');

        $receiver = Mockery::mock(User::class)->makePartial();
        $receiver->id = 'receiver-id-123';

        $service->userUpdated(
            $user,
            $receiver,
            ['name' => 'Old Name'],
            ['name' => 'Changed Name', 'email' => 'changed.email@mailinator.com']
        );

        $this->assertTrue(true);
    }

    /** @test */
    public function can_create_user_deleted_notification(): void
    {
        $fcmMock = Mockery::mock(FCM::class);

        $notificationModelMock = Mockery::mock(Notification::class)->makePartial();
        $notificationModelMock->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($params) {
                return $params['type'] === 'user_deleted';
            }))
            ->andReturn($notificationModelMock);

        $service = new NotificationService($fcmMock, $notificationModelMock);

        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('getAttribute')->with('name')->andReturn('Test User');
        $user->shouldReceive('toArray')->andReturn(['id' => 'user-id-123', 'name' => 'Test User']);

        $receiver = Mockery::mock(User::class)->makePartial();
        $receiver->id = 'receiver-id-123';

        $service->userDeleted($user, $receiver);

        $this->assertTrue(true);
    }

    /** @test */
    public function can_send_notification_with_push_notification(): void
    {
        $fcmMock = Mockery::mock(FCM::class);
        $fcmMock->shouldReceive('sendMulti')
            ->once()
            ->andReturn(true);

        $service = new NotificationService($fcmMock);

        $notification = Mockery::mock(Notification::class)->makePartial();
        $notification->title = 'Test Title';
        $notification->message = 'Test Message';
        $notification->payload = null;
        $notification->shouldReceive('save')->once()->andReturn(true);

        $firebaseToken1 = (object) ['token' => 'firebase-token-1'];
        $firebaseToken2 = (object) ['token' => 'firebase-token-2'];

        $firebaseTokensCollection = collect([$firebaseToken1, $firebaseToken2]);

        $receiver = Mockery::mock(User::class)->makePartial();
        $receiver->firebaseTokens = $firebaseTokensCollection;

        $service->sendPushNotification($receiver, $notification);

        $this->assertTrue(true);
    }
}
