<?php

namespace Modules\Inventory\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QrCatalog extends Model
{
    protected $fillable = ['token', 'category_id', 'generated_by'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
