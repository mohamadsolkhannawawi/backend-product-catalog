<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $primaryKey = 'category_id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = ['name', 'slug', 'description', 'icon', 'parent_id'];
    
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'category_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'category_id');
    }
    
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->category_id)) {
                $model->category_id = (string) Str::uuid();
            }
            if (empty($model->slug) && $model->name) {
                $model->slug = Str::slug($model->name);
            }
        });
    }
}

