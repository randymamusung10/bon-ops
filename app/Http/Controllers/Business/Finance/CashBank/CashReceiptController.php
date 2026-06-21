<?php

namespace App\Http\Controllers\Business\Finance\CashBank;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Business\Finance\CashTransactionRequest;
use App\Services\Business\Finance\CashTransactionService;
use App\Repositories\Business\Finance\CashTransactionRepository;
use App\Models\Business\Finance\ChartOfAccount\ChartOfAccount;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class CashReceiptController extends Controller
{
    protected $service;
    protected $repository;

    public function __construct(CashTransactionService $service, CashTransactionRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index()
    {
        return view('pages.business.finance.cash-receipt.index');
    }

    public function data()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $query = $this->repository->datatable($tenantId, 'receipt');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('date', function ($model) {
                return \Carbon\Carbon::parse($model->date)->format('d/m/Y');
            })
            ->editColumn('total_amount', function ($model) {
                return 'Rp ' . number_format($model->total_amount, 2, ',', '.');
            })
            ->addColumn('status_badge', function($row) {
                $statusMap = [
                    'draft' => '<span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill">Draft</span>',
                    'submitted' => '<span class="badge bg-warning-subtle text-warning px-2 py-1 rounded-pill">Submitted</span>',
                    'approved' => '<span class="badge bg-info-subtle text-info px-2 py-1 rounded-pill">Approved</span>',
                    'posted' => '<span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">Posted</span>',
                    'void' => '<span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill">Void</span>',
                ];
                return $statusMap[$row->status] ?? $row->status;
            })
            ->addColumn('action', function($row) {
                return '<button type="button" class="btn btn-sm btn-light border btn-show" data-uuid="'.$row->uuid.'"><i class="bi bi-eye"></i> Detail</button>';
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function create()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $cashAccounts = ChartOfAccount::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->where('is_header', false)
            ->where(function($q) {
                $q->where('name', 'like', '%Kas%')->orWhere('name', 'like', '%Bank%');
            })->get();
            
        $otherAccounts = ChartOfAccount::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->where('is_header', false)
            ->get();

        return view('pages.business.finance.cash-receipt.partials.create_modal', compact('cashAccounts', 'otherAccounts'));
    }

    public function store(CashTransactionRequest $request)
    {
        try {
            $data = $request->validated();
            $data['type'] = 'receipt';
            
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = $file->store('cash-transactions', 'public');
                $data['attachment_path'] = $path;
            }

            $this->service->createDraft($data);
            return response()->json([
                'success' => true,
                'message' => 'Penerimaan Kas berhasil disimpan sebagai Draft.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function edit($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $transaction = $this->repository->findByUuid($tenantId, $uuid);
        
        if ($transaction->status !== 'draft') {
            return response()->json(['message' => 'Hanya Penerimaan Kas Draft yang dapat diedit.'], 403);
        }

        $cashAccounts = ChartOfAccount::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->where('is_header', false)
            ->where(function($q) {
                $q->where('name', 'like', '%Kas%')->orWhere('name', 'like', '%Bank%');
            })->get();
            
        $otherAccounts = ChartOfAccount::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->where('is_header', false)
            ->get();

        return view('pages.business.finance.cash-receipt.partials.edit_modal', compact('transaction', 'cashAccounts', 'otherAccounts'));
    }

    public function update(CashTransactionRequest $request, $uuid)
    {
        try {
            $data = $request->validated();
            $data['type'] = 'receipt';
            
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = $file->store('cash-transactions', 'public');
                $data['attachment_path'] = $path;
            }

            $this->service->updateDraft($uuid, $data);
            return response()->json([
                'success' => true,
                'message' => 'Penerimaan Kas berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function show($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $transaction = $this->repository->findByUuid($tenantId, $uuid);
        
        return view('pages.business.finance.cash-receipt.partials.show_modal', compact('transaction'));
    }

    public function destroy($uuid)
    {
        try {
            $this->service->delete($uuid);
            return response()->json([
                'success' => true,
                'message' => 'Draft Penerimaan Kas berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function submit($uuid)
    {
        try {
            $this->service->submitDocument($uuid);
            return response()->json(['success' => true, 'message' => 'Penerimaan Kas berhasil diajukan.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function approve($uuid)
    {
        try {
            $this->service->approveDocument($uuid);
            return response()->json(['success' => true, 'message' => 'Penerimaan Kas berhasil disetujui.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function post($uuid)
    {
        try {
            $this->service->postDocument($uuid);
            return response()->json(['success' => true, 'message' => 'Penerimaan Kas berhasil diposting. Buku besar telah terupdate.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
