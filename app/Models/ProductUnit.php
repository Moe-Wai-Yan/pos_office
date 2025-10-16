<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUnit extends Model
{
    use HasFactory;
    protected $fillable = ['variant_id','unit_id','sell_price','is_default'];

    public function unit(){
return $this->belongsTo(Unit::class, 'unit_id', 'unit_id');
}
}
