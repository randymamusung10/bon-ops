<?php

namespace App\Http\Controllers\Business\Crm\CrmMembership;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CrmMembershipController extends Controller
{
    public function index()
    {
        return view('pages.business.crm.membership.index');
    }

    public function data(Request $request)
    {
        $tenantId = auth()->user()->tenant_id ?? 1;
        $query = \App\Models\Business\Crm\CrmMembership::where('tenant_id', $tenantId);

        return datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '<button onclick="editMembership(' . $row->id . ')" class="btn btn-sm btn-primary" title="Edit"><i class="bi bi-pencil"></i></button>';
                $btn .= ' <button onclick="deleteMembership(' . $row->id . ')" class="btn btn-sm btn-danger" title="Hapus"><i class="bi bi-trash"></i></button>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create()
    {
        return view('pages.business.crm.membership.partials.create_modal');
    }

    public function store(Request $request)
    {
        if ($request->filled('minimum_spend')) {
            $request->merge(['minimum_spend' => str_replace('.', '', $request->minimum_spend)]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'minimum_spend' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|string|in:active,inactive',
        ]);

        $tenantId = auth()->user()->tenant_id ?? 1;

        \App\Models\Business\Crm\CrmMembership::create([
            'tenant_id' => $tenantId,
            'name' => $request->name,
            'minimum_spend' => $request->minimum_spend ?? 0,
            'discount_percentage' => $request->discount_percentage ?? 0,
            'status' => $request->status,
        ]);

        return response()->json(['success' => true, 'message' => 'Membership berhasil ditambahkan']);
    }

    public function edit($id)
    {
        $tenantId = auth()->user()->tenant_id ?? 1;
        $membership = \App\Models\Business\Crm\CrmMembership::where('tenant_id', $tenantId)->findOrFail($id);
        return view('pages.business.crm.membership.partials.edit_modal', compact('membership'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'minimum_spend' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|string|in:active,inactive',
        ]);

        $tenantId = auth()->user()->tenant_id ?? 1;
        $membership = \App\Models\Business\Crm\CrmMembership::where('tenant_id', $tenantId)->findOrFail($id);

        $membership->update([
            'name' => $request->name,
            'minimum_spend' => $request->minimum_spend ?? 0,
            'discount_percentage' => $request->discount_percentage ?? 0,
            'status' => $request->status,
        ]);

        return response()->json(['success' => true, 'message' => 'Membership berhasil diubah']);
    }

    public function destroy($id)
    {
        $tenantId = auth()->user()->tenant_id ?? 1;
        $membership = \App\Models\Business\Crm\CrmMembership::where('tenant_id', $tenantId)->findOrFail($id);
        
        // Check if used in customers
        if ($membership->customers()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Membership ini tidak dapat dihapus karena masih digunakan oleh pelanggan'], 400);
        }

        $membership->delete();
        return response()->json(['success' => true, 'message' => 'Membership berhasil dihapus']);
    }
}
