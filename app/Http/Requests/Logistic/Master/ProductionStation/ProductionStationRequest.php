<?php

namespace App\Http\Requests\Logistic\Master\ProductionStation;

use Illuminate\Foundation\Http\FormRequest;

class ProductionStationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama stasiun produksi wajib diisi.',
            'status.in' => 'Status tidak valid.',
        ];
    }
}
