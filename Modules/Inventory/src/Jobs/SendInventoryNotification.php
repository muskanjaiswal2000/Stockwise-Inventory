<?php

namespace Modules\Inventory\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendInventoryNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 2;

    public function __construct(
        public string $recipient,
        public Mailable $mail,
    ) {
        $this->onQueue(config('inventory.notifications.queue', 'notifications'));
    }

    public function handle(): void
    {
        Mail::to($this->recipient)->send($this->mail);
    }
}
