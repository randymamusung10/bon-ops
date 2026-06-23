<?php

namespace App\Http\Controllers\Business\Crm\CrmVoucher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CrmVoucherController extends Controller
{
    public function index()
    {
        return view('pages.business.crm.voucher.index');
    }

    public function data(Request $request)
    {
        $tenantId = auth()->user()->tenant_id ?? 1;
        $query = \App\Models\Business\Crm\CrmVoucher::where('tenant_id', $tenantId);

        return datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '<button onclick="editVoucher(' . $row->id . ')" class="btn btn-sm btn-primary" title="Edit"><i class="bi bi-pencil"></i></button>';
                $btn .= ' <button onclick="deleteVoucher(' . $row->id . ')" class="btn btn-sm btn-danger" title="Hapus"><i class="bi bi-trash"></i></button>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create()
    {
        return view('pages.business.crm.voucher.partials.create_modal');
    }

    public function store(Request $request)
    {
        if ($request->filled('value')) {
            $request->merge(['value' => str_replace('.', '', $request->value)]);
        }
        if ($request->filled('minimum_spend')) {
            $request->merge(['minimum_spend' => str_replace('.', '', $request->minimum_spend)]);
        }
        if ($request->filled('maximum_discount')) {
            $request->merge(['maximum_discount' => str_replace('.', '', $request->maximum_discount)]);
        }

        $tenantId = auth()->user()->tenant_id ?? 1;

        $request->validate([
            'code' => 'required|string|max:255|unique:crm_vouchers,code,NULL,id,tenant_id,' . $tenantId,
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,nominal',
            'value' => 'required|numeric|min:0',
            'minimum_spend' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'quota' => 'nullable|integer|min:0',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'status' => 'required|in:active,inactive',
        ]);

        \App\Models\Business\Crm\CrmVoucher::create([
            'tenant_id' => $tenantId,
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'type' => $request->type,
            'value' => $request->value,
            'minimum_spend' => $request->minimum_spend ?? 0,
            'maximum_discount' => $request->maximum_discount,
            'quota' => $request->quota,
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
            'status' => $request->status,
        ]);

        return response()->json(['success' => true, 'message' => 'Voucher berhasil ditambahkan']);
    }

    public function edit($id)
    {
        $tenantId = auth()->user()->tenant_id ?? 1;
        $voucher = \App\Models\Business\Crm\CrmVoucher::where('tenant_id', $tenantId)->findOrFail($id);
        return view('pages.business.crm.voucher.partials.edit_modal', compact('voucher'));
    }

    public function update(Request $request, $id)
    {
        if ($request->filled('value')) {
            $request->merge(['value' => str_replace('.', '', $request->value)]);
        }
        if ($request->filled('minimum_spend')) {
            $request->merge(['minimum_spend' => str_replace('.', '', $request->minimum_spend)]);
        }
        if ($request->filled('maximum_discount')) {
            $request->merge(['maximum_discount' => str_replace('.', '', $request->maximum_discount)]);
        }

        $tenantId = auth()->user()->tenant_id ?? 1;
        $voucher = \App\Models\Business\Crm\CrmVoucher::where('tenant_id', $tenantId)->findOrFail($id);

        $request->validate([
            'code' => 'required|string|max:255|unique:crm_vouchers,code,' . $id . ',id,tenant_id,' . $tenantId,
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,nominal',
            'value' => 'required|numeric|min:0',
            'minimum_spend' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'quota' => 'nullable|integer|min:0',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'status' => 'required|in:active,inactive',
        ]);

        $voucher->update([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'type' => $request->type,
            'value' => $request->value,
            'minimum_spend' => $request->minimum_spend ?? 0,
            'maximum_discount' => $request->maximum_discount,
            'quota' => $request->quota,
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
            'status' => $request->status,
        ]);

        return response()->json(['success' => true, 'message' => 'Voucher berhasil diubah']);
    }

    public function destroy($id)
    {
        $tenantId = auth()->user()->tenant_id ?? 1;
        $voucher = \App\Models\Business\Crm\CrmVoucher::where('tenant_id', $tenantId)->findOrFail($id);
        $voucher->delete();
        return response()->json(['success' => true, 'message' => 'Voucher berhasil dihapus']);
    }
}
