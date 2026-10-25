<?php

namespace Modules\Inventory\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Inventory\Models\StockMovement;

class StockMovementMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public StockMovement $movement) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Stock movement recorded');
    }

    public function content(): Content
    {
        return new Content(view: 'inventory::emails.stock-movement');
    }
}
