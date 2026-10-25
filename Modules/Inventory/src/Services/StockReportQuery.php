<?php

namespace Modules\Inventory\Services;

use Illuminate\Database\Eloquent\Builder;
use Modules\Inventory\Models\StockMovement;

class StockReportQuery
{
    public function build(?string $dateFrom = null, ?string $dateTo = null, ?string $movementType = null): Builder
    {
        $query = StockMovement::query()
            ->with(['product', 'user']);

        if ($dateFrom !== null && $dateFrom !== '') {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo !== null && $dateTo !== '') {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if ($movementType === 'adjustment') {
            $query->where('is_adjustment', true);
        } elseif ($movementType === 'in' || $movementType === 'out') {
            $query->where('is_adjustment', false)
                ->where('type', $movementType);
        }

        return $query->latest()->orderByDesc('id');
    }
}
