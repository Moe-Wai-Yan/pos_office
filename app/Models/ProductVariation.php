<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasFactory;

    public function variation()
    {
        return $this->belongsTo(Variation::class, 'variation_id', 'id');
    }
    public function type()
    {
        return $this->belongsTo(VariationType::class, 'variation_type_id', 'id');
    }
}
