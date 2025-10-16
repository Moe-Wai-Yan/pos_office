<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable=['product_id','sku','barcode','default_cost','default_price','default_currency','tax_rate_id','is_active'];

   public function product(){
return $this->belongsTo(Product::class, 'product_id', 'product_id');
}


public function attributes(){
return $this->hasMany(VariantAttribute::class, 'variant_id', 'variant_id');
}


public function prices(){
return $this->hasMany(ProductPrice::class, 'variant_id', 'variant_id');
}


public function units(){
return $this->hasMany(ProductUnit::class, 'variant_id', 'variant_id');
}

}
