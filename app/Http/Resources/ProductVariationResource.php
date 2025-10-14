<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "product_id" => $this->product_id,
            "variation_id" => $this->variation_id,
            "variation_name" => $this->variation->name,
            "variation_type_id" => $this->variation_type_id,
            "variation_type_name" => $this->type->name,
            "price" => $this->price,
            "stock" => $this->stock,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at
        ];
    }
}
