<?php

namespace App\Http\Requests\Business\Finance;

use Illuminate\Foundation\Http\FormRequest;

class CashTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'account_id' => 'required|exists:chart_of_accounts,id',
            'reference_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'items' => 'required|array|min:1',
            'items.*.account_id' => 'required|exists:chart_of_accounts,id',
            'items.*.description' => 'required|string|max:255',
            'items.*.amount' => 'required|numeric|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'account_id.required' => 'Akun Kas/Bank wajib dipilih.',
            'items.required' => 'Minimal harus ada 1 item transaksi.',
            'items.min' => 'Minimal harus ada 1 item transaksi.',
            'items.*.account_id.required' => 'Akun pada item wajib dipilih.',
            'items.*.description.required' => 'Deskripsi item wajib diisi.',
            'items.*.amount.required' => 'Nominal item wajib diisi.',
            'items.*.amount.min' => 'Nominal item harus lebih besar dari 0.',
        ];
    }
}
