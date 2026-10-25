<?php

namespace Modules\Inventory\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Inventory\Events\LowStockDetected;
use Modules\Inventory\Events\ProductCreatedOrUpdated;
use Modules\Inventory\Events\StockMovementRecorded;
use Modules\Inventory\Listeners\SendLowStockAlert;
use Modules\Inventory\Listeners\SendProductChangeNotification;
use Modules\Inventory\Listeners\SendStockMovementNotification;

class InventoryEventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        LowStockDetected::class => [SendLowStockAlert::class],
        ProductCreatedOrUpdated::class => [SendProductChangeNotification::class],
        StockMovementRecorded::class => [SendStockMovementNotification::class],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
    }
} 
