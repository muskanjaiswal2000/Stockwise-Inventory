<?php

namespace Modules\Inventory\Http\Controllers;

use Livewire\Component;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\StockMovement;

class Dashboard extends Component
{
    public function render()
    {
        return view('inventory::livewire.dashboard', [
            'productCount' => Product::count(),
            'lowStockCount' => Product::query()
                ->whereColumn('quantity', '<=', 'reorder_level')
                ->count(),
            'recentMovements' => StockMovement::query()
                ->with(['product', 'user'])
                ->latest()
                ->orderByDesc('id')
                ->take(10)
                ->get(),
        ])->layout('livewire.layout.app');
    }
}
