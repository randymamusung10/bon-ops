<?php

namespace App\Http\Requests\Logistic\Purchasing;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequestRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust with proper permission logic if needed
    }

    public function rules()
    {
        return [
            'date' => 'required|date',
            'expected_date' => 'nullable|date|after_or_equal:date',
            'branch_id' => 'required|exists:branches,id',
            'notes' => 'nullable|string|max:1000',
            
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.notes' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'items.required' => 'Minimal harus ada 1 item yang di-request.',
            'items.*.quantity.min' => 'Kuantitas minimal adalah 0.01.',
        ];
    }
}
