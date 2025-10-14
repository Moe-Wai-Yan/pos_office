<?php

namespace App\Http\Resources;

use App\Http\Resources\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $variation = [];
        $option = [];
        if ($this->productVariation) {
            $variation = [
                'id' => $this->productVariation->id,
                'name' => $this->productVariation->type->name,
            ];
        }
        if ($this->optionVariation) {
            $option = [
                'id' => $this->optionVariation->id,
                'name' => $this->optionVariation->name,
            ];
        }
        return [
            'id' => $this->id,
            "order_id" => $this->order_id,
            "product_id" => $this->product_id,
            "price" => $this->price,
            "quantity" => $this->quantity,
            "total_price" => $this->total_price,
            "created_at" => $this->created_at ?? '',
            "product" => new ProductResource($this->product),
            "order_variation" => count($variation) > 0 ? $variation : null,
            "order_variation_option" => count($option) > 0 ? $option : null,
        ];
    }
}
