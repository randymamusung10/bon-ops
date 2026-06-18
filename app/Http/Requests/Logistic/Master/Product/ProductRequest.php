<?php

namespace App\Http\Requests\Logistic\Master\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'product_category_id' => 'nullable|exists:product_categories,id',
            'unit_id' => 'nullable|exists:units,id',
            'type' => 'nullable|string|in:finished_good,raw_material,service',
            'price' => 'nullable|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
            'inventory_account_id' => 'nullable|exists:chart_of_accounts,id',
            'cogs_account_id' => 'nullable|exists:chart_of_accounts,id',
            'income_account_id' => 'nullable|exists:chart_of_accounts,id',
            'tax_id' => 'nullable|exists:taxes,id',
        ];
    }
}
