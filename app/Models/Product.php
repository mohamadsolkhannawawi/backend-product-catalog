<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'seller_id',
        'product_id',
        'primary_image',
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

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->product_id)) {
                $model->product_id = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'product_id';
    }

    /**
     * Use product_id (UUID string) as primary key
     */
    protected $primaryKey = 'product_id';
    public $incrementing = false;
    protected $keyType = 'string';
}
