<h1>Low stock alert</h1>

<p>Product <strong>{{ $product->name }}</strong> is at or below its reorder level.</p>

<ul>
    <li>SKU: {{ $product->sku }}</li>
    <li>Current quantity: {{ $product->quantity }} {{ $product->unit }}</li>
    <li>Reorder level: {{ $product->reorder_level }} {{ $product->unit }}</li>
</ul>

@if ($movement)
    <p>This alert was triggered after a {{ $movement->is_adjustment ? 'stock adjustment' : 'stock movement' }}.</p>
@endif
<h1>Low stock alert</h1>

<p>The following product has reached or fallen below its reorder level:</p>

<ul>
    <li><strong>Product:</strong> {{ $product->name }}</li>
    <li><strong>SKU:</strong> {{ $product->sku }}</li>
    <li><strong>Current quantity:</strong> {{ number_format($product->quantity) }} {{ $product->unit }}</li>
    <li><strong>Reorder level:</strong> {{ number_format($product->reorder_level) }} {{ $product->unit }}</li>
</ul>

@if ($movement)
    <h2>Movement that triggered this alert</h2>
    <ul>
        <li><strong>Type:</strong> {{ $movement->is_adjustment ? 'Adjustment' : ($movement->type === 'in' ? 'Stock in' : 'Stock out') }}</li>
        <li><strong>Quantity:</strong> {{ number_format($movement->quantity) }} {{ $product->unit }}</li>
        <li><strong>Reason:</strong> {{ $movement->reason ?: 'Not provided' }}</li>
        <li><strong>Recorded by:</strong> {{ $movement->user?->name ?? 'Unknown' }}</li>
    </ul>
@endif

<p>Please review the product and restock it when necessary.</p>
