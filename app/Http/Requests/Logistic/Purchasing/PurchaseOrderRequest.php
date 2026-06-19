<?php

namespace App\Http\Requests\Logistic\Purchasing;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Handle authorization via middleware or policies if needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'branch_id' => 'required|exists:branches,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'date' => 'required|date',
            'expected_date' => 'nullable|date|after_or_equal:date',
            'notes' => 'nullable|string',
            
            // Items validation
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'items.required' => 'Minimal satu item produk harus ditambahkan.',
            'items.min' => 'Minimal satu item produk harus ditambahkan.',
            'items.*.product_id.required' => 'Produk pada baris wajib dipilih.',
            'items.*.unit_id.required' => 'Satuan pada baris wajib dipilih.',
            'items.*.quantity.required' => 'Kuantitas pada baris wajib diisi.',
            'items.*.quantity.min' => 'Kuantitas pada baris tidak boleh nol atau negatif.',
            'items.*.unit_price.required' => 'Harga Satuan pada baris wajib diisi.',
        ];
    }
}
