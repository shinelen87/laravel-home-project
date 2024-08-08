<?php

namespace App\Notifications\Wishlist;

use App\Mail\NewPriceMail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

class NewPriceNotification extends Notification
{
    use Queueable;

    public function __construct(public Product $product)
    {
        $this->onQueue('wishlist-notifications');
    }

    public function via(User $user): array
    {
        return ['mail'];
    }

    public function toMail(User $user): Mailable
    {
        return (new NewPriceMail($this->product, $user))->to($user->email);
    }
}
