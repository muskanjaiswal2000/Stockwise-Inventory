<?php

namespace Modules\Inventory\Services;

use Illuminate\Database\Eloquent\Builder;
use Modules\Inventory\Models\StockMovement;

class StockReportQuery
{
    public function build(
        ?string $dateFrom = null,
        ?string $dateTo = null,
        ?string $movementType = null
    ): Builder {
        $query = StockMovement::query()
            ->with([
                'product:id,name,sku,unit',
                'user:id,name',
            ]);

        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom . ' 00:00:00');
        }

        if ($dateTo) {
            $query->where(
                'created_at',
                '<',
                \Carbon\Carbon::parse($dateTo)->addDay()->startOfDay()
            );
        }

        if ($movementType === 'adjustment') {
            $query->where('is_adjustment', true);
        } elseif (in_array($movementType, ['in', 'out'], true)) {
            $query->where('is_adjustment', false)
                ->where('type', $movementType);
        }

        return $query->orderByDesc('created_at')
            ->orderByDesc('id');
    }
}
