<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
        $rules =  [
            'name' => 'required','max:255',
            // 'price' => 'numeric',
            'images'         => 'required|array|min:1|max:10',
            'images.*'       => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id'=>'required|exists:sub_categories,id',
            'brand_id' => 'required|exists:brands,id',
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
             'images.required' => 'Please upload at least 1 image.',
            'images.max' => 'You can upload a maximum of 10 images.',
            'images.*.image' => 'Each file must be an image.',
            'images.*.max' => 'Each image must be no larger than 10MB.',
        ];
    }
}
