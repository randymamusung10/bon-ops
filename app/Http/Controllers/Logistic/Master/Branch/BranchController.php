<?php

namespace App\Http\Controllers\Logistic\Master\Branch;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
    /**
     * Tampilkan halaman utama manajemen cabang.
     */
    public function index()
    {
        return view('pages.logistic.master.branch.index');
    }

    /**
     * Tampilkan form modal tambah cabang (Create).
     */
    public function create()
    {
        return view('pages.logistic.master.branch.partials.create_modal');
    }

    /**
     * Ambil data cabang untuk server-side DataTables.
     */
    public function data()
    {
        $user = Auth::user();
        
        $branches = Branch::with('company')
            ->where('tenant_id', $user->tenant_id)
            ->where('company_id', $user->company_id)
            ->select(['id', 'tenant_id', 'company_id', 'uuid', 'code', 'name', 'city', 'address', 'status'])
            ->latest();

        return DataTables::of($branches)
            ->filterColumn('company.name', function($query, $keyword) {
                $query->whereHas('company', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('status', function($query, $keyword) {
                if ($keyword !== '') {
                    $query->where('status', $keyword);
                }
            })
            ->make(true);
    }

    /**
     * Simpan cabang baru ke database.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                // Kode harus unik per tenant
                Rule::unique('branches', 'code')->where(function ($query) use ($user) {
                    return $query->where('tenant_id', $user->tenant_id)
                                 ->whereNull('deleted_at');
                }),
            ],
            'name' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ]);

        Branch::create([
            'tenant_id' => $user->tenant_id,
            'company_id' => $user->company_id,
            'code' => $validated['code'],
            'name' => $validated['name'],
            'city' => $validated['city'],
            'address' => $validated['address'],
            'status' => 'active', // default aktif
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cabang baru berhasil ditambahkan.'
        ]);
    }

    /**
     * Ambil detail satu cabang berdasarkan UUID.
     */
    public function show($uuid)
    {
        $user = Auth::user();
        
        $branch = Branch::where('uuid', $uuid)
            ->where('tenant_id', $user->tenant_id)
            ->where('company_id', $user->company_id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $branch
        ]);
    }

    /**
     * Tampilkan form modal edit cabang (Edit).
     */
    public function edit($uuid)
    {
        $user = Auth::user();
        
        $branch = Branch::where('uuid', $uuid)
            ->where('tenant_id', $user->tenant_id)
            ->where('company_id', $user->company_id)
            ->firstOrFail();

        return view('pages.logistic.master.branch.partials.edit_modal', compact('branch'));
    }

    /**
     * Perbarui data cabang di database.
     */
    public function update(Request $request, $uuid)
    {
        $user = Auth::user();

        $branch = Branch::where('uuid', $uuid)
            ->where('tenant_id', $user->tenant_id)
            ->where('company_id', $user->company_id)
            ->firstOrFail();

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('branches', 'code')->where(function ($query) use ($user) {
                    return $query->where('tenant_id', $user->tenant_id)
                                 ->whereNull('deleted_at');
                })->ignore($branch->id),
            ],
            'name' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $branch->update([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'city' => $validated['city'],
            'address' => $validated['address'],
            'status' => $validated['status'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data cabang berhasil diperbarui.'
        ]);
    }

    /**
     * Hapus cabang (soft delete) berdasarkan UUID.
     */
    public function destroy($uuid)
    {
        $user = Auth::user();

        $branch = Branch::where('uuid', $uuid)
            ->where('tenant_id', $user->tenant_id)
            ->where('company_id', $user->company_id)
            ->firstOrFail();

        $branch->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cabang berhasil dihapus.'
        ]);
    }
}
