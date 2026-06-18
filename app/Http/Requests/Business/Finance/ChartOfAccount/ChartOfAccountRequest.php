<?php

namespace App\Http\Requests\Business\Finance\ChartOfAccount;

use Illuminate\Foundation\Http\FormRequest;

class ChartOfAccountRequest extends FormRequest
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
            'account_type' => 'required|in:asset,liability,equity,revenue,expense',
            'status' => 'nullable|in:active,inactive',
            'is_header' => 'nullable|boolean',
            'parent_id' => 'nullable|exists:chart_of_accounts,id'
        ];
    }
}
