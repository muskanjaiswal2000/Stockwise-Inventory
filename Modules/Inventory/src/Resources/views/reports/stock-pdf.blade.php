<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stock report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1e293b; font-size: 10px; }
        h1 { color: #312e81; margin-bottom: 4px; }
        p { color: #64748b; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        th { background: #eef2ff; color: #3730a3; text-align: left; }
        th, td { border: 1px solid #dbeafe; padding: 7px; }
        tr:nth-child(even) { background: #f8fafc; }
    </style>
</head>
<body>
    <h1>Stock movement report</h1>
    <p>Generated {{ now()->format('M j, Y g:i A') }}</p>
    <table>
        <thead>
            <tr>
                <th>Date</th><th>Product</th><th>SKU</th><th>Movement</th>
                <th>Quantity</th><th>Unit</th><th>Reason</th><th>Recorded by</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($movements as $movement)
                <tr>
                    <td>{{ $movement->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $movement->product?->name ?? 'Product unavailable' }}</td>
                    <td>{{ $movement->product?->sku ?? '' }}</td>
                    <td>{{ $movement->is_adjustment ? 'Adjustment' : ($movement->type === 'in' ? 'Stock in' : 'Stock out') }}</td>
                    <td>{{ $movement->quantity }}</td>
                    <td>{{ $movement->product?->unit ?? '' }}</td>
                    <td>{{ $movement->reason ?? '' }}</td>
                    <td>{{ $movement->user?->name ?? 'Unknown' }}</td>
                </tr>
            @empty
                <tr><td colspan="8">No stock movements found for the selected filters.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
