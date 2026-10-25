<?php

namespace Modules\Inventory\Http\Controllers\Stock;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Modules\Inventory\Services\StockReportQuery;

class ExportPdf
{
    public function __invoke(Request $request, StockReportQuery $reportQuery)
    {
        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'movement_type' => ['nullable', 'in:in,out,adjustment'],
        ]);

        $movements = $reportQuery->build(
            $filters['date_from'] ?? null,
            $filters['date_to'] ?? null,
            $filters['movement_type'] ?? null,
        )->get();

        return Pdf::loadView('inventory::reports.stock-pdf', [
            'movements' => $movements,
            'filters' => $filters,
        ])->setPaper('a4', 'landscape')->download('stock-report.pdf');
    }
}
