<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Product;
use Laravolt\Indonesia\Models\Provinsi;
use Illuminate\Support\Str;

class Review extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'name',
        'email',
        'province_id',
        'phone',
        'rating',
        'comment',
        'review_id',
    ];

    /**
     * Casts for attributes.
     * Ensure rating is treated as integer when serializing.
     */
    protected $casts = [
        'rating' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function province()
    {
        return $this->belongsTo(Provinsi::class, 'province_id');
    }

    /**
     * Return a primitive snapshot of the review suitable for notifications
     * (safe to serialize for queued jobs).
     *
     * @return array<string,mixed>
     */
    public function toSnapshot(): array
    {
        $productName = null;
        if ($this->relationLoaded('product')) {
            $productName = $this->product ? $this->product->name : null;
        } else {
            // attempt to get product name without forcing heavy operations
            try {
                $productName = $this->product ? $this->product->name : null;
            } catch (\Throwable $e) {
                $productName = null;
            }
        }

        return [
            'review_id' => $this->review_id,
            'product_id' => $this->product_id,
            'product_name' => $productName,
            'name' => $this->name,
            'email' => $this->email,
            'rating' => $this->rating !== null ? (int) $this->rating : null,
            'comment' => $this->comment,
            'province_id' => $this->province_id,
        ];
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->review_id)) {
                $model->review_id = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'review_id';
    }

    protected $primaryKey = 'review_id';
    public $incrementing = false;
    protected $keyType = 'string';
}
