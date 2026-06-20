<?php

namespace App\Http\Requests\Logistic\Master\Recipe;

use Illuminate\Foundation\Http\FormRequest;

class RecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'production_station_id' => 'nullable|exists:production_stations,id',
            'name' => 'required|string|max:150',
            'quantity' => 'required|numeric|min:0.0001',
            'status' => 'required|in:draft,active,inactive',
            
            // Item details validation
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.unit_id' => 'required|exists:units,id',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Menu produk wajib dipilih.',
            'product_id.exists' => 'Menu produk tidak terdaftar.',
            'name.required' => 'Nama resep wajib diisi.',
            'quantity.required' => 'Jumlah output resep wajib diisi.',
            'quantity.min' => 'Jumlah output tidak boleh kurang dari 0.0001.',
            'status.required' => 'Status resep wajib diisi.',
            'items.required' => 'Bahan-bahan resep wajib ditambahkan.',
            'items.min' => 'Resep harus memiliki minimal 1 bahan.',
            'items.*.product_id.required' => 'Bahan baku wajib dipilih.',
            'items.*.quantity.required' => 'Jumlah bahan baku wajib diisi.',
            'items.*.unit_id.required' => 'Satuan bahan baku wajib dipilih.',
        ];
    }
}
