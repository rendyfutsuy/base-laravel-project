<?php

namespace Modules\Hierarchy\Providers;

use Modules\Hierarchy\Listeners\RoleSynchronized\AddRoleSynchronizedLogToUser;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Hierarchy\Listeners\RoleSynchronized\AddRoleSynchronizedLogToSuperAdmin;
use Modules\Hierarchy\Listeners\RoleSynchronized\SendEmailRoleSynchronizedNotificationToUser;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'permission.stored' => [],

        'permission.resynchronized' => [],

        'permission.synchronized' => [],

        'role.stored' => [],

        'role.resynchronized' => [],

        'role.synchronized' => [
            SendEmailRoleSynchronizedNotificationToUser::class,
            AddRoleSynchronizedLogToSuperAdmin::class,
            AddRoleSynchronizedLogToUser::class,
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
