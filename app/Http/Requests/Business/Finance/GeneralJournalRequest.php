<?php

namespace App\Http\Requests\Business\Finance;

use Illuminate\Foundation\Http\FormRequest;

class GeneralJournalRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Asumsikan otorisasi ditangani middleware
    }

    public function rules()
    {
        $rules = [
            'date' => 'required|date',
            'reference_type' => 'nullable|string|max:100',
            'reference_id' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // max 5MB
            'items' => 'required|array|min:2',
            'items.*.chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'items.*.debit' => 'required|numeric|min:0',
            'items.*.credit' => 'required|numeric|min:0',
            'items.*.description' => 'nullable|string|max:255',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'items.min' => 'Jurnal harus memiliki setidaknya dua baris (Debit dan Kredit).',
            'items.*.chart_of_account_id.required' => 'Akun COA wajib diisi pada setiap baris.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);
            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($items as $item) {
                $totalDebit += (float) ($item['debit'] ?? 0);
                $totalCredit += (float) ($item['credit'] ?? 0);
            }

            if (round($totalDebit, 2) !== round($totalCredit, 2)) {
                $validator->errors()->add('items', 'Total Debit (Rp ' . number_format($totalDebit, 2, ',', '.') . ') dan Kredit (Rp ' . number_format($totalCredit, 2, ',', '.') . ') harus seimbang (Balance).');
            }
        });
    }
}
