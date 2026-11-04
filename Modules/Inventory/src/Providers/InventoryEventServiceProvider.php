<?php

namespace Modules\Inventory\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Inventory\Events\LowStockDetected;
use Modules\Inventory\Events\CategoryCreated;
use Modules\Inventory\Events\ProductCreatedOrUpdated;
use Modules\Inventory\Events\StockMovementRecorded;
use Modules\Inventory\Listeners\SendLowStockAlert;
use Modules\Inventory\Listeners\SendProductChangeNotification;
use Modules\Inventory\Listeners\StoreLowStockNotification;
use Modules\Inventory\Listeners\SendStockMovementNotification;
use Modules\Inventory\Listeners\StoreProductNotification;
use Modules\Inventory\Listeners\StoreStockMovementNotification;

class InventoryEventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        LowStockDetected::class => [SendLowStockAlert::class, StoreLowStockNotification::class],
        CategoryCreated::class => [StoreCategoryNotification::class],
        ProductCreatedOrUpdated::class => [SendProductChangeNotification::class, StoreProductNotification::class],
        StockMovementRecorded::class => [SendStockMovementNotification::class, StoreStockMovementNotification::class],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
    }
} 
