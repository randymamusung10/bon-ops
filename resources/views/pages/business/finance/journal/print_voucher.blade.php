<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Journal Voucher - {{ $journal->journal_number }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #1f2937; margin: 0; padding: 20px; }
        .document-header { width: 100%; border-bottom: 2px solid #e5e7eb; padding-bottom: 15px; margin-bottom: 20px; text-align: center; }
        .document-header h1 { font-size: 18px; margin: 0 0 5px 0; color: #111827; text-transform: uppercase; letter-spacing: 0.5px; }
        .document-header p { margin: 0; font-size: 11px; color: #4b5563; }
        .document-header h2 { font-size: 14px; margin: 10px 0 5px 0; color: #111827; text-transform: uppercase; }
        
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 11px; }
        .table th, .table td { padding: 6px 10px; text-align: left; }
        .table th { background-color: #f8fafc; border-bottom: 2px solid #e5e7eb; color: #4b5563; font-weight: 600; }
        .border-bottom td { border-bottom: 1px solid #e5e7eb; }
        .border-top td { border-top: 1px solid #e5e7eb; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        
        .info-table { width: 100%; margin-bottom: 20px; font-size: 11px; }
        .info-table td { padding: 4px; vertical-align: top; }
        .info-table .label { font-weight: 600; width: 120px; color: #4b5563; }
        
        .signatures { width: 100%; margin-top: 50px; font-size: 11px; }
        .signatures td { text-align: center; width: 33%; vertical-align: bottom; height: 80px; }
        .signature-line { border-top: 1px solid #1f2937; width: 80%; margin: 0 auto; padding-top: 5px; font-weight: 600; }
    </style>
</head>
<body>

    <div class="document-header">
        <h1 style="margin-bottom: 10px;">{{ Auth::user()->tenant->name ?? 'BONOPS CORP' }}</h1>
        <h2>Journal Voucher</h2>
        <p>No. Jurnal: {{ $journal->journal_number }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Status</td>
            <td>: <span style="text-transform: uppercase;">{{ $journal->status }}</span></td>
            <td class="label">Tanggal</td>
            <td>: {{ \Carbon\Carbon::parse($journal->date)->format('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Referensi</td>
            <td>: {{ $journal->reference_type }} {{ $journal->reference_id ? ' - '.$journal->reference_id : '' }}</td>
            <td class="label">Dibuat Oleh</td>
            <td>: {{ $journal->creator->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Keterangan</td>
            <td colspan="3">: {{ $journal->notes ?? '-' }}</td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="30%">Kode & Nama Akun</th>
                <th width="35%">Keterangan Baris</th>
                <th width="15%" class="text-right">Debit (Rp)</th>
                <th width="15%" class="text-right">Kredit (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($journal->items as $index => $item)
            <tr class="border-bottom">
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->account->code ?? '' }} - {{ $item->account->name ?? '' }}</td>
                <td>{{ $item->description }}</td>
                <td class="text-right">{{ number_format($item->debit, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->credit, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="border-top" style="background-color: #f9fafb;">
                <td colspan="3" class="text-right fw-bold" style="padding: 10px;">TOTAL KESELURUHAN</td>
                <td class="text-right fw-bold" style="padding: 10px;">{{ number_format($journal->total_debit, 0, ',', '.') }}</td>
                <td class="text-right fw-bold" style="padding: 10px;">{{ number_format($journal->total_credit, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <table class="signatures">
        <tr>
            <td>
                <div class="signature-line">Dibuat Oleh</div>
                <div style="font-size:10px; margin-top:5px; color: #4b5563;">{{ $journal->creator->name ?? '-' }}</div>
            </td>
            <td>
                <div class="signature-line">Diperiksa Oleh</div>
                <div style="font-size:10px; margin-top:5px; color: #4b5563;">(Penyelia Keuangan)</div>
            </td>
            <td>
                <div class="signature-line">Disetujui Oleh</div>
                <div style="font-size:10px; margin-top:5px; color: #4b5563;">(Manajer Keuangan)</div>
            </td>
        </tr>
    </table>

    <script type="text/php">
        if ( isset($pdf) ) {
            $x = $pdf->get_width() / 2 - 40;
            $y = $pdf->get_height() - 35;
            $text = "Halaman {PAGE_NUM} dari {PAGE_COUNT} | Dicetak: {{ now()->format('d/m/Y H:i') }}";
            $font = $fontMetrics->get_font("helvetica", "normal");
            $size = 8;
            $color = array(0.5, 0.5, 0.5);
            $pdf->page_text($x, $y, $text, $font, $size, $color);
        }
    </script>
</body>
</html>
