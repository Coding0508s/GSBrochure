<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brochure extends Model
{
    protected $fillable = ['name', 'stock', 'last_stock_quantity', 'last_stock_date'];

    protected $casts = [
        'stock' => 'integer',
        'last_stock_quantity' => 'integer',
    ];

    public function requestItems(): HasMany
    {
        return $this->hasMany(RequestItem::class, 'brochure_id');
    }

    public function stockHistory(): HasMany
    {
        return $this->hasMany(StockHistory::class, 'brochure_id');
    }
}
