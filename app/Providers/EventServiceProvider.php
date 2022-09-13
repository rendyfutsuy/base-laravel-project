<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use App\Listeners\SendExampleDeleteNotification;
use App\Listeners\SendExampleUpdateNotification;
use App\Listeners\SendExampleCreationNotification;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        'example.created' => [
            SendExampleCreationNotification::class,
        ],

        'example.updated' => [
            SendExampleUpdateNotification::class,
        ],

        'example.deleted' => [
            SendExampleDeleteNotification::class,
        ],

        'permission.stored' => [],

        'permission.resynchronized' => [],

        'role.stored' => [],

        'role.resynchronized' => [],

        'user.created' => [],

        'user.updated' => [],

        'user.deleted' => [],
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
