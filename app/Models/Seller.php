<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Seller extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'store_name',
        'store_description',
        'phone',
        'address',
        'rt',
        'rw',
        'province_id',
        'city_id',
        'district_id',
        'village_id',
        'ktp_number',
        'ktp_file_path',
        'pic_file_path',
        'seller_id',
        'status',
        'rejection_reason',
        'verified_at',
        'is_active',
    ];


    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        // Seller.user_id references users.user_id (both are UUID representative columns)
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Relasi ke Provinsi (Laravolt)
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(\Laravolt\Indonesia\Models\Province::class, 'province_id', 'code');
    }

    /**
     * Relasi ke Kota/Kabupaten (Laravolt)
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(\Laravolt\Indonesia\Models\City::class, 'city_id', 'code');
    }

    /**
     * Relasi ke Kecamatan (Laravolt)
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(\Laravolt\Indonesia\Models\District::class, 'district_id', 'code');
    }

    /**
     * Relasi ke Kelurahan/Desa (Laravolt)
     */
    public function village(): BelongsTo
    {
        return $this->belongsTo(\Laravolt\Indonesia\Models\Village::class, 'village_id', 'code');
    }

    /**
     * Relasi ke Products milik Seller
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->seller_id)) {
                $model->seller_id = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'seller_id';
    }

    /**
     * Use seller_id as primary key
     */
    protected $primaryKey = 'seller_id';
    public $incrementing = false;
    protected $keyType = 'string';
}
