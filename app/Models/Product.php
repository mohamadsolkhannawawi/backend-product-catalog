<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'seller_id',
        'name',
        'slug',
        'description',
        'category',
        'price',
        'stock',
        'images',
        'visitor',
        'is_active',
    ];


    protected $casts = [
        'images'   => 'array', // JSON array
        'price'    => 'decimal:2',
        'visitor'  => 'integer',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
