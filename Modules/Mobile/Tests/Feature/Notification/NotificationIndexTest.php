<?php

namespace Modules\Mobile\Tests\Feature\Notification;

use Tests\TestCase;
use Tests\Feature\Components\AuthCase;

class NotificationIndexTest extends TestCase
{
    use AuthCase;

    /** @test */
    public function user_can_access_notification_index(): void
    {
        $currentUser = $this->login('superadmin@mailinator.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentUser['token'],
        ])->getJson(route('api.notification.mobile.index'));

        $response->assertOk();
    }
}
