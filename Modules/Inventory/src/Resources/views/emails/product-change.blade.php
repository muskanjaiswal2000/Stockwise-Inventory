<h1>Product {{ $action }}</h1>

<p>A product was {{ $action }} in the inventory system.</p>

<ul>
    <li>Name: {{ $product->name }}</li>
    <li>SKU: {{ $product->sku }}</li>
    <li>Quantity: {{ $product->quantity }} {{ $product->unit }}</li>
    <li>Selling price: {{ number_format((float) $product->selling_price, 2) }}</li>
</ul>
