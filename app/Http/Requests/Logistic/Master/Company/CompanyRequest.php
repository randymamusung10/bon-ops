<?php

namespace App\Http\Requests\Logistic\Master\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $uuid = $this->route('uuid');

        $rules = [
            'name' => 'required|string|max:255',
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nama Perusahaan',
            'status' => 'Status',
        ];
    }
}
