<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWarehousePermission extends Model
{
    use HasFactory;
    protected $fillable=['user_id','warehouse_id','can_sell','can_purchase','can_adjust_stock','can_view_reports','can_custom_price'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function warehouse(){
        return $this->belongsTo(Warehouse::class);
    }
}
