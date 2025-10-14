<?php

namespace App\Rules;

use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Contracts\Validation\Rule;

class CartProductExists implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $carts = json_decode($value);
        foreach ($carts as $cart) {
            $product = Product::find($cart->product_id);
            if ($product) {
                $checkStock = $product;
                if ($cart->product_variation_id) {
                    $checkStock = ProductVariation::find($cart->product_variation_id);
                }
                if(!$checkStock) {
                    return false;
                }
                return true;
            } else {
                return false;
            }
        }
        // $products = Product::select('id')->active()->instock()->whereIn('id', $cartProducts)->get()->pluck('id')->toArray();
        // $productCheck = array_diff($cartProducts, $products);
        // if (count($productCheck)) {
        //     return false;
        // }
        // return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Ordered Product not found.';
    }
}
