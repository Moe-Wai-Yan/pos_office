<?php

namespace App\Http\Resources;

use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

         $productVariation = ProductVariation::where('id', $this->product_variation_id)
            ->first();
        $variationName = '';
        $variationTypeName = '';
        $product = Product::find($this->product_id);
        if ($this->product_variation_id) {
            $product = ProductVariation::find($this->product_variation_id);
            $variationName = $productVariation->variation->name;
            $variationTypeName = $productVariation->type->name;
        }


        return [
            'id' => $this->id,
            'image' => $this->product->image ?? '',
            'product_id' => $this->product_id,
            'product_name' => $this->product->name ?? '---',
            'product_variation_id' => $this->product_variation_id,
            'option_id'=>$this->option_id,
            'product_variation_name' => $productVariation ? "$variationName : $variationTypeName" : '---',
            'quantity' => $this->quantity,
            'discount_price' => $this->price ?? 0,
            'price' => $this->price,
            'wholesale_price' => $product->wholesale_price ?? 0,
            'sub_total' => $this->quantity * $this->price,
        ];
    }
}
