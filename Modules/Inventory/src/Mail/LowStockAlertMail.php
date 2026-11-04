<?php

namespace Modules\Inventory\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LowStockAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public object $product,
        public ?object $movement = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Low stock alert: {$this->product->name}"
        );
    }

    public function content(): Content
    {
        return new Content(view: 'inventory::emails.low-stock-alert');
    }
}