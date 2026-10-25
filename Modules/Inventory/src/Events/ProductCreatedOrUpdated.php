<?php

namespace Modules\Inventory\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Inventory\Models\Product;

class ProductCreatedOrUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Product $product,
        public string $action,
    ) {}
}
