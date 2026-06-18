<?php

namespace App\Http\Requests\Business\Finance\Currency;

use Illuminate\Foundation\Http\FormRequest;

class CurrencyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'code' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            'symbol' => 'nullable|string|max:10',
            'exchange_rate' => 'required|numeric|min:0',
            'status' => 'nullable|in:active,inactive'
        ];
    }
}
