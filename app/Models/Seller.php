<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Seller extends Model
{
    protected $fillable = [
        'user_id',
        'store_name',
        'phone',
        'nid_number',
        'nid_image_path',
        'province_id',
        'city_id',
        'district_id',
        'village_id',
        'address',
        'status',
    ];

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
}
