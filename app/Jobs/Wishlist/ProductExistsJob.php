<?php

namespace App\Jobs\Wishlist;

use App\Enums\WishType;
use App\Notifications\Wishlist\ProductAvailableNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProductExistsJob extends BaseJob
{
    public function handle(): void
    {
        $this->sendNotifications(
            ProductAvailableNotification::class,
            WishType::EXIST
        );
    }
}
