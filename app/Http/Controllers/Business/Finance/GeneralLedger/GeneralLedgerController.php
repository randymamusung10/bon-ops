<?php

namespace App\Http\Controllers\Business\Finance\GeneralLedger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Business\Finance\GeneralLedgerService;
use App\Repositories\Business\Finance\GeneralLedgerRepository;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class GeneralLedgerController extends Controller
{
    protected $service;
    protected $repository;

    public function __construct(GeneralLedgerService $service, GeneralLedgerRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index()
    {
        return view('pages.business.finance.ledger.index');
    }

    public function data(Request $request)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $accountId = $request->input('account_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        if ($request->boolean('is_reset')) {
            return DataTables::of(collect([]))->make(true);
        }

        $beginningBalance = 0;
        $isDebitNormal = true;
        
        if ($accountId) {
            $account = \App\Models\Business\Finance\ChartOfAccount\ChartOfAccount::find($accountId);
            $isDebitNormal = in_array($account->account_type, ['asset', 'expense']);
            $beginning = $this->repository->getBeginningBalance($tenantId, $accountId, $startDate);
            $beginningBalance = $isDebitNormal 
                ? ($beginning['total_debit'] - $beginning['total_credit'])
                : ($beginning['total_credit'] - $beginning['total_debit']);
        }

        $query = $this->repository->datatable($tenantId, $accountId, $startDate, $endDate, $beginningBalance, $isDebitNormal);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('account', function ($model) {
                return '[' . ($model->account->code ?? '-') . '] ' . ($model->account->name ?? '-');
            })
            ->editColumn('date', function ($model) {
                return \Carbon\Carbon::parse($model->date)->format('d/m/Y');
            })
            ->editColumn('debit', function ($model) {
                return 'Rp ' . number_format($model->debit, 2, ',', '.');
            })
            ->editColumn('credit', function ($model) {
                return 'Rp ' . number_format($model->credit, 2, ',', '.');
            })
            ->addColumn('running_balance', function($model) use ($accountId) {
                if (!$accountId) return '-';
                return 'Rp ' . number_format($model->running_balance, 2, ',', '.');
            })
            ->addColumn('reference', function($model) {
                $ref = $model->source_type;
                if ($model->source_type === 'App\Models\Business\Finance\GeneralJournal\GeneralJournal') {
                    if ($model->source) {
                        $url = route('business.finance.journal.show', $model->source->uuid);
                        return '<a href="'.$url.'" class="text-primary text-decoration-none fw-medium show-btn">Jurnal Umum #'.$model->source->journal_number.'</a>';
                    }
                    $ref = 'Jurnal Umum';
                } elseif ($model->source_type === 'App\Models\Logistic\Purchasing\PurchaseOrder') {
                    $ref = 'Purchase Order';
                } elseif ($model->source_type === 'App\Models\Operational\Pos\PosOrder') {
                    $ref = 'POS Order';
                }
                return $ref . ' #' . $model->source_id;
            })
            ->rawColumns(['reference'])
            ->make(true);
    }

    public function summary(Request $request)
    {
        try {
            if ($request->boolean('is_reset')) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'beginning_balance' => 0,
                        'total_debit' => 0,
                        'total_credit' => 0,
                        'ending_balance' => 0
                    ]
                ]);
            }

            $tenantId = Auth::user()->tenant_id ?? 1;
            $accountId = $request->input('account_id');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $summary = $this->service->getSummary($tenantId, $accountId, $startDate, $endDate);

            return response()->json([
                'success' => true,
                'data' => $summary
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function print(Request $request)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $accountId = $request->input('account_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $beginningBalance = 0;
        $isDebitNormal = true;
        $account = null;
        
        if ($accountId) {
            $account = \App\Models\Business\Finance\ChartOfAccount\ChartOfAccount::find($accountId);
            $isDebitNormal = in_array($account->account_type, ['asset', 'expense']);
            $beginning = $this->repository->getBeginningBalance($tenantId, $accountId, $startDate);
            $beginningBalance = $isDebitNormal 
                ? ($beginning['total_debit'] - $beginning['total_credit'])
                : ($beginning['total_credit'] - $beginning['total_debit']);
        }

        $query = $this->repository->datatable($tenantId, $accountId, $startDate, $endDate, $beginningBalance, $isDebitNormal);
        $ledgers = $query->get();

        $summary = $this->service->getSummary($tenantId, $accountId, $startDate, $endDate);
        
        $user = Auth::user()->load('company', 'branch', 'tenant');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pages.business.finance.ledger.print', compact('ledgers', 'summary', 'account', 'startDate', 'endDate', 'user'));
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->stream('Buku_Besar_' . date('Ymd_His') . '.pdf');
    }
}
