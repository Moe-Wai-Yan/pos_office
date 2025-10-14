<?php

namespace App\Models;

use App\Casts\Color;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Currency;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'sizes' => 'array',
        'myanmar_colors' => 'array',
        'english_colors' => 'array',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }




    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }

    public function image()
    {
        return $this->hasOne(ProductImage::class, 'product_id', 'id')->latestOfMany();
    }

    public function volumePrices()
    {
        return $this->hasMany(VolumePricing::class, 'product_id', 'id');
    }

    public function variations()
    {
        return $this->hasMany(ProductVariation::class, 'product_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', '1');
    }

}
