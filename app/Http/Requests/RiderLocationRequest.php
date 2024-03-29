<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RiderLocationRequest extends FormRequest
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
            'rider_id' => 'required|exists:riders,id',
            'service_name' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'timestamp' => 'required|date'
        ];
    }
}
