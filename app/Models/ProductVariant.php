<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable=['product_id','sku','barcode','default_cost','default_price','default_currency','tax_rate_id','is_active'];
}
