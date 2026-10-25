<h1>Stock movement recorded</h1>

<p>A stock movement was recorded for <strong>{{ $movement->product?->name ?? 'a product' }}</strong>.</p>

<ul>
    <li>Type: {{ $movement->is_adjustment ? 'Adjustment' : ucfirst($movement->type) }}</li>
    <li>Quantity: {{ $movement->quantity }}</li>
    <li>Reason: {{ $movement->reason ?: 'No reason provided' }}</li>
    <li>Recorded by: {{ $movement->user?->name ?? 'Unknown user' }}</li>
</ul>
