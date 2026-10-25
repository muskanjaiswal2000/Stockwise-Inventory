<?php

namespace Modules\Inventory\Http\Controllers\Stock;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Inventory\Exports\StockReportExport;

class ExportExcel
{
    public function __invoke(Request $request)
    {
        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'movement_type' => ['nullable', 'in:in,out,adjustment'],
        ]);

        return Excel::download(
            new StockReportExport(
                $filters['date_from'] ?? null,
                $filters['date_to'] ?? null,
                $filters['movement_type'] ?? null,
            ),
            'stock-report.xlsx'
        );
    }
}
