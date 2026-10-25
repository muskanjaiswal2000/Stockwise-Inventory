<?php

namespace Modules\Inventory\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\StockMovement;

class LowStockAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Product $product,
        public ?StockMovement $movement = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Low stock alert: {$this->product->name}");
    }

    public function content(): Content
    {
        return new Content(view: 'inventory::emails.low-stock-alert');
    }
}
