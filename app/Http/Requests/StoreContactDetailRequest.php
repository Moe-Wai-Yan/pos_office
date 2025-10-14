<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactDetailRequest extends FormRequest
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
            'email'=>'required|email|max:255',
            'phone'=>'required|max:20',
            'address'=>'required|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'messenger_url' => 'nullable|url|max:255',
            'viber_url' => 'nullable|url|max:255',
            'tiktok_url'=>'nullable|url|max:255'
        ];
    }
}
