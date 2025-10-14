<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
        $rules = [
            'name' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
             'sub_category_id' => 'required|exists:sub_categories,id',
             'price' => 'required_if:product_type,1|nullable|numeric|min:0',
             'stock' => 'required_if:product_type,1|nullable|numeric|min:0',
             'description' => 'required|string',
            // 'myanmar_colors' => 'array',
            // 'myanmar_colors.*' => 'exists:product_colors,myanmar_name',
            // 'english_colors' => 'array',
            // 'english_colors.*' => 'exists:product_colors,english_name',
            // 'sizes' => 'array',
            // 'sizes.*' => 'exists:product_sizes,name',
        ];

        return $rules;
    }

    public function messages(){
        return [
            // 'sizes.exists' => 'Product Size does not exists',
        ];
    }
}
