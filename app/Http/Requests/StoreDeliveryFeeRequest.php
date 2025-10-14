<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeliveryFeeRequest extends FormRequest
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
        if ($this->delivery_fee) {
            return [
                'region_id' => 'required|exists:regions,id',
                'city' => 'required|max:255|unique:delivery_fees,city,' . $this->delivery_fee->id . ',id,deleted_at,NULL',
                'fee' => 'required|numeric',
            ];
        } else {
            return [
                'region_id' => 'required|exists:regions,id',
                'city' => 'required|max:255|unique:delivery_fees,city,NULL,id,deleted_at,NULL',
                'fee' => 'required|numeric',
            ];
        }
    }
}
