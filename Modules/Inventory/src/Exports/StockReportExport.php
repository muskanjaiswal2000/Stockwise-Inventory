<?php

namespace Modules\Inventory\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\Inventory\Services\StockReportQuery;

class StockReportExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    public function __construct(
        private readonly ?string $dateFrom = null,
        private readonly ?string $dateTo = null,
        private readonly ?string $movementType = null,
    ) {
    }

    public function collection(): Collection
    {
        return app(StockReportQuery::class)
            ->build($this->dateFrom, $this->dateTo, $this->movementType)
            ->get();
    }

    public function headings(): array
    {
        return ['Date', 'Product', 'SKU', 'Movement', 'Quantity', 'Unit', 'Reason', 'Recorded by'];
    }

    public function map($movement): array
    {
        return [
            $movement->created_at->format('Y-m-d H:i'),
            $movement->product?->name ?? 'Product unavailable',
            $movement->product?->sku ?? '',
            $movement->is_adjustment ? 'Adjustment' : ($movement->type === 'in' ? 'Stock in' : 'Stock out'),
            $movement->quantity,
            $movement->product?->unit ?? '',
            $movement->reason ?? '',
            $movement->user?->name ?? 'Unknown',
        ];
    }
}
