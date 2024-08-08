<?php

namespace App\Jobs\Wishlist;

use App\Enums\WishType;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

abstract class BaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Product $product)
    {
        $this->onQueue('wishlist');
    }

    abstract public function handle(): void;

    protected function sendNotifications(string $notificationClass, WishType $type = WishType::PRICE): void
    {
        $this->product->followers()
            ->wherePivot($type->value, true)
            ->chunk(
                500,
                fn(Collection $users) => Notification::send(
                    $users,
                    app($notificationClass, [
                        'product' => $this->product
                    ])
                )
            );
    }
}
