<?php

namespace App\Mail;

use App\Models\Product;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class NewPriceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Product $product, public User $user)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            cc: ['admin@admin.com'],
            subject: 'New Price Mail'
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.new-price-mail',
            with: [
                'url' => url(route('products.show', $this->product)),
                'user' => $this->user,
                'product' => $this->product
            ]
        );
    }

    public function attachments(): array
    {
        $filePath = str_replace('/storage', '', $this->product->thumbnail);

        if (Storage::exists($filePath)) {
            return [Attachment::fromPath(Storage::path($filePath))];
        }

        return [];
    }
}
