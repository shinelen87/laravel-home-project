<?php

namespace App\Jobs\Wishlist;

use App\Enums\WishType;
use App\Notifications\Wishlist\NewPriceNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PriceUpdatedJob extends BaseJob
{
    public function handle(): void
    {
        $this->sendNotifications(
            NewPriceNotification::class,
            WishType::PRICE
        );
    }
}
