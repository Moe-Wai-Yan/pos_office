<?php

namespace App\Models;

use App\Casts\Color;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Currency;
use App\Models\ProductImage;
use Google\Service\AndroidPublisher\Variant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category_id', 'supplier_id', 'has_variants', 'default_expiry_days', 'description', 'is_active'];

    protected $casts = [
        'sizes' => 'array',
        'myanmar_colors' => 'array',
        'english_colors' => 'array',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
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

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'product_id');
    }


    public function defaultVariant()
    {
        return $this->hasOne(ProductVariant::class, 'product_id', 'product_id')->where('is_default', true);
    }


    public function units()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', '1');
    }

}
