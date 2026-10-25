<?php

namespace Modules\Inventory\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseRequestReturn extends Model
{
    protected $fillable = [
        'purchase_request_item_id', 'processed_by', 'quantity', 'condition',
        'status', 'notes', 'processed_at',
    ];

    protected $casts = ['quantity' => 'integer', 'processed_at' => 'datetime'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequestItem::class, 'purchase_request_item_id');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
