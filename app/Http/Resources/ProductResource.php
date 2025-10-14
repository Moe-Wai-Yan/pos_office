<?php

namespace App\Http\Resources;

use App\Models\ProductVariation;
use App\Models\VariationType;
use App\Models\Wishlist;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $variations = ProductVariation::where('product_id', $this->id)->get();
        $product_variations = [];

        foreach ($variations as $variation) {
            $optionData = [];
            // Fetch options based on option_type_ids
            $options = collect(json_decode($variation->option_type_ids))
                ->map(function ($id) {
                    return VariationType::find($id)->name;
                })
                ->toArray();

            foreach (json_decode($variation->price) as $key => $price) {
                // Create an option array for this specific variation
                if($variation->option_type_ids) {
                    $optionData = [
                        'id' => (int)(json_decode($variation->option_type_ids)[$key]),
                        'name' => $options[$key],
                        'quantity' => (int)(json_decode($variation->stock)[$key] ?? 0),
                        'color'=>$variation->color,
                        'price' => (int)$price,
                         'discount_price' => (int)(json_decode($variation->discount_price)[$key] ?? 0),
                    ];
                }

                // Use a combination of variation ID and type to group
                $variationKey = $variation->variation->id . '-' . $variation->type->id;

                if (!isset($product_variations[$variationKey])) {
                    // Create a new variation object if it doesn't exist
                    $product_variations[$variationKey] = [
                        'id' => $variation->id,
                        'name' => $variation->variation->name,
                        'type' => $variation->type->name,
                        'price' => $variation->option_type_ids ? 0 : (int)$price,

                        'stock' => $variation->option_type_ids ? 0 : (int)(json_decode($variation->stock)[$key]),
                        'options' => [],
                    ];
                }

                // Append the option to the correct variation
                if($optionData != []) {
                    $product_variations[$variationKey]['options'][] = $optionData;
                }
            }
        }

        $product_variations = array_values($product_variations);
        $userId = 0;
        if ($request->user('api')) {
            $userId = $request->user('api')->id;
        }
        $wishlist = Wishlist::where('product_id', $this->id)->where('customer_id', $userId)->first();
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "brand" => $this->brand ? $this->brand->name : '',
            "category" => $this->category ? $this->category->name : '',
            'sub_category'=>$this->subCategory ? $this->subCategory->name : '',
            "price" => (int)$this->price ?? 0,
            "volume_prices" => $this->volumePrices,
            "status" => $this->status,
            "images" => $this->images->pluck('path')->toArray(),
            "stock" => $this->stock,
              'discount_price'=>$this->discount_price,
            "variations" => count($product_variations) > 0 ? $product_variations : null,
            'is_wishlist' => $wishlist ? 1 : 0,
            // "variations" => ProductVariationResource::collection($this->variations),
            "created_at" => $this->created_at,
        ];
    }
}
