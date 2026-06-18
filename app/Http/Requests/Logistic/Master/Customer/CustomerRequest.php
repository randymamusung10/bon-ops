<?php

namespace App\Http\Requests\Logistic\Master\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_person_phone' => 'nullable|string|max:255',
            'credit_limit' => 'nullable|numeric|min:0',
            'account_receivable_id' => 'nullable|exists:chart_of_accounts,id',
            'default_currency_id' => 'nullable|exists:currencies,id',
            'tax_id' => 'nullable|exists:taxes,id',
            'status' => 'required|in:active,inactive',
        ];
    }
}
