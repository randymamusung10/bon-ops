<?php

namespace App\Http\Controllers\Business\Finance\GeneralJournal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Business\Finance\GeneralJournalRequest;
use App\Services\Business\Finance\GeneralJournalService;
use App\Repositories\Business\Finance\GeneralJournalRepository;
use App\Models\Business\Finance\ChartOfAccount\ChartOfAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class GeneralJournalController extends Controller
{
    protected $service;
    protected $repository;

    public function __construct(GeneralJournalService $service, GeneralJournalRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index()
    {
        return view('pages.business.finance.journal.index');
    }

    public function data()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $query = $this->repository->datatable($tenantId);

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('date', function ($model) {
                return \Carbon\Carbon::parse($model->date)->format('d/m/Y');
            })
            ->editColumn('total_debit', function ($model) {
                return 'Rp ' . number_format($model->total_debit, 2, ',', '.');
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
        $accounts = ChartOfAccount::where('tenant_id', $tenantId)->where('status', 'active')->where('is_header', false)->get();

        return view('pages.business.finance.journal.partials.create_modal', compact('accounts'));
    }

    public function store(GeneralJournalRequest $request)
    {
        try {
            $data = $request->validated();
            
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = $file->store('journals', 'public');
                $data['attachment_path'] = $path;
            }

            $this->service->createDraft($data);
            return response()->json([
                'success' => true,
                'message' => 'Jurnal Umum berhasil disimpan sebagai Draft.'
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
        $journal = $this->repository->findByUuid($tenantId, $uuid);
        
        if ($journal->status !== 'draft') {
            return response()->json(['message' => 'Hanya jurnal Draft yang dapat diedit.'], 403);
        }

        $accounts = ChartOfAccount::where('tenant_id', $tenantId)->where('status', 'active')->where('is_header', false)->get();

        $referenceText = $journal->reference_id;
        if ($journal->reference_type === 'PurchaseOrder' && $journal->reference_id) {
            $po = \App\Models\Logistic\Purchasing\PurchaseOrder::where('tenant_id', $tenantId)->where('po_number', $journal->reference_id)->first();
            if ($po) {
                $date = $po->date ? \Carbon\Carbon::parse($po->date)->format('d/m/Y') : '-';
                $supplier = $po->supplier_name ?: 'Pemasok Umum';
                $amount = number_format($po->total_amount, 0, ',', '.');
                $referenceText = "{$po->po_number} | {$date} | {$supplier} | Rp {$amount}";
            }
        } elseif ($journal->reference_type === 'PosOrder' && $journal->reference_id) {
            $pos = \App\Models\Operational\Pos\PosOrder::where('tenant_id', $tenantId)->where('order_number', $journal->reference_id)->first();
            if ($pos) {
                $date = $pos->date ? \Carbon\Carbon::parse($pos->date)->format('d/m/Y') : '-';
                $customer = $pos->customer_name ?: 'Pelanggan Umum';
                $amount = number_format($pos->grand_total, 0, ',', '.');
                $referenceText = "{$pos->order_number} | {$date} | {$customer} | Rp {$amount}";
            }
        }

        return view('pages.business.finance.journal.partials.edit_modal', compact('journal', 'accounts', 'referenceText'));
    }

    public function update(GeneralJournalRequest $request, $uuid)
    {
        try {
            $data = $request->validated();
            
            if ($request->hasFile('attachment')) {
                // Delete old file if needed, handled by logic or garbage collection, but for now just upload new
                $file = $request->file('attachment');
                $path = $file->store('journals', 'public');
                $data['attachment_path'] = $path;
            }

            $this->service->updateDraft($uuid, $data);
            return response()->json([
                'success' => true,
                'message' => 'Jurnal Umum berhasil diperbarui.'
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
        $journal = $this->repository->findByUuid($tenantId, $uuid);
        
        return view('pages.business.finance.journal.partials.show_modal', compact('journal'));
    }

    public function destroy($uuid)
    {
        try {
            $this->service->delete($uuid);
            return response()->json([
                'success' => true,
                'message' => 'Jurnal Draft berhasil dihapus.'
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
            return response()->json(['success' => true, 'message' => 'Jurnal berhasil diajukan untuk persetujuan.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function approve($uuid)
    {
        try {
            $this->service->approveDocument($uuid);
            return response()->json(['success' => true, 'message' => 'Jurnal berhasil disetujui.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function post($uuid)
    {
        try {
            $this->service->postDocument($uuid);
            return response()->json(['success' => true, 'message' => 'Jurnal berhasil diposting. Buku besar telah terupdate.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function printVoucher($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $journal = $this->repository->findByUuid($tenantId, $uuid);

        if (!in_array($journal->status, ['approved', 'posted'])) {
            abort(403, 'Jurnal belum disetujui, tidak bisa dicetak.');
        }

        $pdf = Pdf::loadView('pages.business.finance.journal.print_voucher', compact('journal'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("Journal_Voucher_{$journal->journal_number}.pdf");
    }

    public function getReferences(Request $request)
    {
        $type = $request->query('type');
        $tenantId = Auth::user()->tenant_id ?? 1;
        $search = $request->query('q', '');
        
        $results = [];

        if ($type === 'PurchaseOrder') {
            $query = \App\Models\Logistic\Purchasing\PurchaseOrder::with('supplier')->where('tenant_id', $tenantId);
            if (!empty($search)) {
                $query->where('po_number', 'like', "%{$search}%");
            }
            $data = $query->orderBy('id', 'desc')->limit(50)->get();
            
            foreach ($data as $po) {
                $supplierName = $po->supplier ? $po->supplier->name : 'Tanpa Supplier';
                $date = $po->date ? \Carbon\Carbon::parse($po->date)->format('d/m/Y') : '-';
                $amount = number_format($po->total_amount, 0, ',', '.');
                
                $text = "{$po->po_number} | {$date} | {$supplierName} | Rp {$amount}";
                $results[] = ['id' => $po->po_number, 'text' => $text];
            }
        } elseif ($type === 'PosOrder') {
            $query = \App\Models\Operational\Pos\PosOrder::where('tenant_id', $tenantId);
            if (!empty($search)) {
                $query->where('order_number', 'like', "%{$search}%");
            }
            $data = $query->orderBy('id', 'desc')->limit(50)->get();
            
            foreach ($data as $pos) {
                $date = $pos->date ? \Carbon\Carbon::parse($pos->date)->format('d/m/Y') : '-';
                $customer = $pos->customer_name ?: 'Pelanggan Umum';
                $amount = number_format($pos->grand_total, 0, ',', '.');
                
                $text = "{$pos->order_number} | {$date} | {$customer} | Rp {$amount}";
                $results[] = ['id' => $pos->order_number, 'text' => $text];
            }
        }

        return response()->json($results);
    }

    public function getReferenceDetails(Request $request)
    {
        $type = $request->query('type');
        $id = $request->query('id');
        $tenantId = Auth::user()->tenant_id ?? 1;
        
        $items = [];

        if ($type === 'PurchaseOrder') {
            $po = \App\Models\Logistic\Purchasing\PurchaseOrder::where('tenant_id', $tenantId)->where('po_number', $id)->first();
            if ($po) {
                // Cari akun persediaan dan hutang (pastikan bukan header)
                $persediaan = \App\Models\Business\Finance\ChartOfAccount\ChartOfAccount::where('tenant_id', $tenantId)
                    ->where('is_header', false)
                    ->where('name', 'like', '%Persediaan%')
                    ->first();
                $hutang = \App\Models\Business\Finance\ChartOfAccount\ChartOfAccount::where('tenant_id', $tenantId)
                    ->where('is_header', false)
                    ->where(function($q) {
                        $q->where('name', 'like', 'Hutang%')->orWhere('name', 'like', 'Utang%');
                    })->first();
                
                if ($persediaan) {
                    $items[] = [
                        'account_id' => $persediaan->id,
                        'description' => "Persediaan dari PO: {$po->po_number}",
                        'debit' => (int) $po->total_amount,
                        'credit' => 0
                    ];
                }
                
                if ($hutang) {
                    $items[] = [
                        'account_id' => $hutang->id,
                        'description' => "Hutang Usaha atas PO: {$po->po_number}",
                        'debit' => 0,
                        'credit' => (int) $po->total_amount
                    ];
                }
            }
        } elseif ($type === 'PosOrder') {
            $pos = \App\Models\Operational\Pos\PosOrder::where('tenant_id', $tenantId)->where('order_number', $id)->first();
            if ($pos) {
                // Cari akun kas dan pendapatan (pastikan bukan header)
                $kas = \App\Models\Business\Finance\ChartOfAccount\ChartOfAccount::where('tenant_id', $tenantId)
                    ->where('is_header', false)
                    ->where(function($q) {
                        $q->where('name', 'like', '%Kas%')->orWhere('name', 'like', '%Bank%');
                    })->first();
                $pendapatan = \App\Models\Business\Finance\ChartOfAccount\ChartOfAccount::where('tenant_id', $tenantId)
                    ->where('is_header', false)
                    ->where(function($q) {
                        $q->where('name', 'like', '%Pendapatan%')->orWhere('name', 'like', '%Penjualan%');
                    })->first();
                
                if ($kas) {
                    $items[] = [
                        'account_id' => $kas->id,
                        'description' => "Penerimaan POS: {$pos->order_number}",
                        'debit' => (int) $pos->grand_total,
                        'credit' => 0
                    ];
                }
                
                if ($pendapatan) {
                    $items[] = [
                        'account_id' => $pendapatan->id,
                        'description' => "Pendapatan POS: {$pos->order_number}",
                        'debit' => 0,
                        'credit' => (int) $pos->grand_total
                    ];
                }
            }
        }

        return response()->json(['success' => true, 'data' => $items]);
    }
}
