<?php

namespace App\Listeners\Orders;

use App\Enums\Role;
use App\Events\OrderCreatedEvent;
use App\Models\User;
use App\Notifications\Admin\OrderCreatedNotification;
use Illuminate\Support\Facades\Notification;

class CreatedListener
{
    public $queue = 'listeners';
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    public function viaQueue(): string
    {
        return 'listeners';
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCreatedEvent $event): void
    {
        logs()->info('Order created', ['order' => $event->order]);
        Notification::send(
            User::role(Role::ADMIN->value)->get(),
            app(OrderCreatedNotification::class, ['order' => $event->order])
        );
    }
}
