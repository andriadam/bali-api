<?php

namespace App\Providers;

use App\Events\NewsCreated;
use App\Events\NewsDeleted;
use App\Events\NewsUpdated;
use App\Listeners\LogNewsCreated;
use App\Listeners\LogNewsDeleted;
use App\Listeners\LogNewsUpdated;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        NewsCreated::class => [
            LogNewsCreated::class,
        ],
        NewsUpdated::class => [
            LogNewsUpdated::class,
        ],
        NewsDeleted::class => [
            LogNewsDeleted::class,
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
