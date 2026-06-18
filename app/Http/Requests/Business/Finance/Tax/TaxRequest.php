<?php

namespace App\Http\Requests\Business\Finance\Tax;

use Illuminate\Foundation\Http\FormRequest;

class TaxRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'rate_percentage' => 'required|numeric|min:0|max:100',
            'status' => 'nullable|in:active,inactive'
        ];
    }
}
