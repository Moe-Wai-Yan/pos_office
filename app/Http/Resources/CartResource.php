<?php

namespace App\Http\Resources;

use App\Models\Product;
use App\Models\Discount;
use App\Models\ProductVariation;
use App\Http\Resources\CartItemResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $totalPrice = 0;
        $discountTotal = 0;
        $wholesaleTotal = 0;
        $percent = 0;
        $this->cartItems->each(function ($item) use (&$totalPrice, &$discount) {
            $product = null;
            if (!$item->product_variation_id) {
                $product = Product::find($item->product_id);
            } else {
                $product = ProductVariation::find($item->product_variation_id);
            }
            $price=json_decode($item->price,true);
            $totalPrice += (int)$item->quantity * $price;
        });
        $discount = Discount::where('target_amount', '<=', $totalPrice)->first();
        if ($discount) {
            $this->cartItems->each(function ($item) use (&$wholesaleTotal) {
                $product = null;
                if (!$item->product_variation_id) {
                    $product = Product::find($item->product_id);
                } else {
                    $product = ProductVariation::find($item->product_variation_id);
                }
                $wholesaleTotal += $item->quantity * $product->wholesale_price;
            });
            if ($wholesaleTotal >= $discount->target_discount_amount) {
                $percent = $discount->percent;
                $discountTotal = ($wholesaleTotal * $percent) / 100;
            }
        }
        return [
            'id' => $this->id,
            'cart_items' => CartItemResource::collection($this->cartItems),
            'total_price' => $totalPrice,
            'wholesale_total' => $wholesaleTotal,
            'discount_percent' => $percent ?? 0,
            'grand_total' => ($discount ? $wholesaleTotal : $totalPrice) - $discountTotal,
        ];
    }
}
