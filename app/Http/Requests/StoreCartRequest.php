<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'cart_id'=>'exists:carts,id',
            'product_id'=>'required|exists:products,id',
            'product_variation_id'=>'exists:product_variations,id',
            'price'=>'required|numeric',
            'sub_total'=>'required|numeric',
            'quantity'=>'required|numeric',
        ];
    }
}
