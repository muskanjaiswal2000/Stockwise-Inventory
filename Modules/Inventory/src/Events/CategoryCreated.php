<?php

namespace Modules\Inventory\Events;

use Modules\Inventory\Models\Category;

class CategoryCreated
{
    public function __construct(
        public Category $category
    ) {}
}
