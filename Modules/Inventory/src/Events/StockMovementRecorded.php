<?php

namespace Modules\Inventory\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Inventory\Models\StockMovement;

class StockMovementRecorded
{
    use Dispatchable, SerializesModels;

    public function __construct(public StockMovement $movement) {}
}
