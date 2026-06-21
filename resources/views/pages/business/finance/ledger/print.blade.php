<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Buku Besar</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #1f2937; margin: 0; padding: 20px; }
        .document-header { width: 100%; border-bottom: 2px solid #e5e7eb; padding-bottom: 15px; margin-bottom: 20px; }
        .document-header table { width: 100%; }
        .company-info h1 { font-size: 18px; margin: 0 0 5px 0; color: #111827; text-transform: uppercase; letter-spacing: 0.5px; }
        .company-info p { margin: 0; font-size: 11px; color: #4b5563; }
        .report-title h2 { font-size: 16px; margin: 0 0 5px 0; color: #111827; text-align: right; text-transform: uppercase; }
        .report-title p { margin: 0; font-size: 11px; color: #6b7280; text-align: right; }
        
        .filter-info { margin-bottom: 20px; background-color: #f9fafb; padding: 12px; border-radius: 4px; border: 1px solid #f3f4f6; }
        .filter-info table { width: 100%; }
        .filter-info td { padding: 4px 0; font-size: 11px; }
        .filter-label { font-weight: 600; color: #4b5563; width: 120px; }
        
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { padding: 8px 10px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        .table th { background-color: #f3f4f6; font-weight: 600; color: #374151; font-size: 10px; text-transform: uppercase; border-top: 1px solid #e5e7eb; border-bottom: 2px solid #e5e7eb; }
        .table tbody tr:nth-child(even) { background-color: #fafafa; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; color: #111827; }
        
        .summary-box { width: 100%; border-collapse: collapse; margin-top: 30px; page-break-inside: avoid; }
        .summary-box th { background-color: #f3f4f6; padding: 10px; text-align: center; font-size: 10px; text-transform: uppercase; color: #374151; border: 1px solid #e5e7eb; }
        .summary-box td { border: 1px solid #e5e7eb; padding: 12px; text-align: center; font-size: 12px; color: #111827; }
        
        .empty-state { text-align: center; padding: 30px; color: #6b7280; font-style: italic; }
    </style>
</head>
<body>

    <div class="document-header">
        <table>
            <tr>
                <td class="company-info" width="60%" valign="top">
                    <h1>{{ $user->company->name ?? $user->tenant->name ?? 'Perusahaan Tidak Diketahui' }}</h1>
                    <p>Cabang: <strong>{{ $user->branch->name ?? 'Pusat' }}</strong></p>
                </td>
                <td class="report-title" width="40%" valign="top">
                    <h2>BUKU BESAR</h2>
                    <p>Dicetak pada: {{ date('d/m/Y H:i') }}<br>Oleh: {{ $user->name }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="filter-info">
        <table>
            <tr>
                <td class="filter-label">Akun (COA)</td>
                <td width="35%">: <strong>{{ $account ? '['.$account->code.'] '.$account->name : 'Semua Akun' }}</strong></td>
                <td class="filter-label" style="width: 80px;">Periode</td>
                <td width="35%">: <strong>{{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Awal' }} s/d {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Akhir' }}</strong></td>
            </tr>
        </table>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="10%">Tanggal</th>
                @if(!$account)
                <th width="15%">Akun</th>
                @endif
                <th width="15%">Referensi</th>
                <th>Deskripsi</th>
                <th width="12%" class="text-right">Debit</th>
                <th width="12%" class="text-right">Kredit</th>
                @if($account)
                <th width="12%" class="text-right">Saldo</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($ledgers as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($row->date)->format('d/m/Y') }}</td>
                @if(!$account)
                <td>[{{ $row->account->code ?? '-' }}] {{ $row->account->name ?? '-' }}</td>
                @endif
                <td>
                    @php
                        $ref = 'Lainnya';
                        if ($row->source_type === 'App\Models\Business\Finance\GeneralJournal\GeneralJournal') $ref = 'Jurnal Umum';
                        elseif ($row->source_type === 'App\Models\Logistic\Purchasing\PurchaseOrder') $ref = 'Purchase Order';
                        elseif ($row->source_type === 'App\Models\Operational\Pos\PosOrder') $ref = 'POS Order';
                    @endphp
                    {{ $ref }} <br> <small style="color: #6b7280;">#{{ $row->source_id }}</small>
                </td>
                <td>{{ $row->description }}</td>
                <td class="text-right">{{ number_format($row->debit, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($row->credit, 2, ',', '.') }}</td>
                @if($account)
                <td class="text-right fw-bold">{{ number_format($row->running_balance, 2, ',', '.') }}</td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="{{ $account ? 7 : 7 }}" class="empty-state">Tidak ada data transaksi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table class="summary-box">
        <thead>
            <tr>
                <th>Saldo Awal</th>
                <th>Total Debit</th>
                <th>Total Kredit</th>
                <th>Saldo Akhir</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="fw-bold">Rp {{ number_format($summary['beginning_balance'], 2, ',', '.') }}</td>
                <td class="fw-bold">Rp {{ number_format($summary['total_debit'], 2, ',', '.') }}</td>
                <td class="fw-bold">Rp {{ number_format($summary['total_credit'], 2, ',', '.') }}</td>
                <td class="fw-bold">Rp {{ number_format($summary['ending_balance'], 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- DomPDF Page numbering script -->
    <script type="text/php">
        if ( isset($pdf) ) {
            $x = $pdf->get_width() / 2 - 40;
            $y = $pdf->get_height() - 35;
            $text = "Halaman {PAGE_NUM} dari {PAGE_COUNT}";
            $font = $fontMetrics->get_font("helvetica", "normal");
            $size = 8;
            $color = array(0.5, 0.5, 0.5);
            $pdf->page_text($x, $y, $text, $font, $size, $color);
        }
    </script>
</body>
</html>
