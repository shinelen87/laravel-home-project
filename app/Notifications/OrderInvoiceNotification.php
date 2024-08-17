<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Storage;

class OrderInvoiceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order, public string $invoicePath)
    {
        $this->onQueue('order-notifications');
    }

    public function via(Order $order): array
    {
        return ['mail'];
    }

    public function toMail(Order $order): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Order Invoice')
            ->greeting("Hello, {$order->name} {$order->lastname}")
            ->line('Thank you for your order.')
            ->line('Please find attached the invoice for your recent order.')
            ->attach($this->invoicePath)
            ->line('Thank you for using our application!');
    }

    public function routeNotificationForMail(Order $order)
    {
        // Return the email address stored in the order
        return $order->email;
    }
}
