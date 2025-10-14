<?php

namespace App\Http\Requests;


use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\FormRequest;

class StoreBannerRequest extends FormRequest
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
        $isUpdate = Route::currentRouteName() == 'banner.update' ? 'nullable' : 'required';
        return [
            'title' => 'required|max:255',
            'image' => [$isUpdate, 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048']
        ];
    }
}
