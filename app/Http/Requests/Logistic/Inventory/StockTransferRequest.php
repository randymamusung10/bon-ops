<?php

namespace App\Http\Requests\Logistic\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StockTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'source_branch_id' => ['required', 'exists:branches,id'],
            'source_warehouse_id' => ['required', 'exists:warehouses,id'],
            'destination_branch_id' => ['required', 'exists:branches,id'],
            'destination_warehouse_id' => ['required', 'exists:warehouses,id', 'different:source_warehouse_id'],
            'notes' => ['nullable', 'string'],
            
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'numeric', 'min:0.01'],
            'items.*.notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'destination_warehouse_id.different' => 'Gudang tujuan tidak boleh sama dengan gudang asal.',
            'items.required' => 'Minimal harus ada 1 produk yang ditransfer.',
            'items.*.product_id.required' => 'Produk wajib dipilih.',
            'items.*.qty.min' => 'Jumlah produk harus lebih dari 0.',
        ];
    }
}
