<?php

namespace Modules\UserManagement\Providers;

use Modules\UserManagement\Listeners\UserCreated\AddUserCreatedLogToUser;
use Modules\UserManagement\Listeners\UserDeleted\AddUserDeletedLogToUser;
use Modules\UserManagement\Listeners\UserUpdated\AddUserUpdatedLogToUser;
use Modules\UserManagement\Listeners\UserCreated\AddUserCreatedLogToSuperAdmin;
use Modules\UserManagement\Listeners\UserDeleted\AddUserDeletedLogToSuperAdmin;
use Modules\UserManagement\Listeners\UserUpdated\AddUserUpdatedLogToSuperAdmin;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\UserManagement\Listeners\UserCreated\SendEmailUserCreatedNotificationToUser;
use Modules\UserManagement\Listeners\UserDeleted\SendEmailUserDeletedNotificationToUser;
use Modules\UserManagement\Listeners\UserUpdated\SendEmailUserUpdatedNotificationToUser;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'user.stored' => [
            SendEmailUserCreatedNotificationToUser::class,
            AddUserCreatedLogToSuperAdmin::class,
            AddUserCreatedLogToUser::class,
        ],

        'user.updated' => [
            SendEmailUserUpdatedNotificationToUser::class,
            AddUserUpdatedLogToSuperAdmin::class,
            AddUserUpdatedLogToUser::class,
        ],

        'user.deleted' => [
            SendEmailUserDeletedNotificationToUser::class,
            AddUserDeletedLogToSuperAdmin::class,
            AddUserDeletedLogToUser::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
