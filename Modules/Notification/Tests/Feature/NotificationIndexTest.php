<?php

namespace Modules\Notification\Tests\Feature;

use Mockery;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Tests\Feature\Components\MockAuthHelper;
use Modules\Notification\Http\Repositories\Contracts\NotificationContract;

class NotificationIndexTest extends TestCase
{
    use MockAuthHelper;

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
    public function user_can_access_notification_index(): void
    {
        $userMock = $this->mockUser('superadmin@mailinator.com', 'Superadmin', 'user-id-123');

        // Mock NotificationRepository
        $notificationRepositoryMock = Mockery::mock(NotificationContract::class);
        $notificationRepositoryMock->shouldReceive('countUnread')
            ->once()
            ->andReturn(0);

        $this->app->instance(NotificationContract::class, $notificationRepositoryMock);

        Passport::actingAs($userMock, ['*'], 'api');

        $response = $this->getJson(route('api.notification.index'));

        $response->assertOk();
    }
}
