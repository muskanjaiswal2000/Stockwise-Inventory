<?php

namespace Modules\Inventory\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\StockMovement;

class LowStockDetected
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Product $product,
        public ?StockMovement $movement = null,
    ) {}
}
