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
        $productSlug = null;
        if ($this->relationLoaded('product')) {
            if ($this->product) {
                $productName = $this->product->name;
                $productSlug = $this->product->slug;
            }
        } else {
            // attempt to get product data without forcing heavy operations
            try {
                if ($this->product) {
                    $productName = $this->product->name;
                    $productSlug = $this->product->slug;
                }
            } catch (\Throwable $e) {
                $productName = null;
                $productSlug = null;
            }
        }

        return [
            'review_id' => $this->review_id,
            'product_id' => $this->product_id,
            'product_name' => $productName,
            'product_slug' => $productSlug,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
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
