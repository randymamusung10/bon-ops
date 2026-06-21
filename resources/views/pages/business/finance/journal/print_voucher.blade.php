<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Journal Voucher - {{ $journal->journal_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 4px;
            vertical-align: top;
        }
        .info-table .label {
            font-weight: bold;
            width: 120px;
        }
        .lines-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .lines-table th, .lines-table td {
            border: 1px solid #000;
            padding: 8px;
        }
        .lines-table th {
            background-color: #f0f0f0;
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .font-bold {
            font-weight: bold;
        }
        .signatures {
            width: 100%;
            margin-top: 50px;
        }
        .signatures td {
            text-align: center;
            width: 33%;
            vertical-align: bottom;
            height: 80px;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 80%;
            margin: 0 auto;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>JOURNAL VOUCHER</h1>
        <div>{{ $journal->tenant->name ?? 'Perusahaan' }} - {{ $journal->branch->name ?? 'Pusat' }}</div>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">No. Jurnal</td>
            <td>: {{ $journal->journal_number }}</td>
            <td class="label">Status</td>
            <td>: {{ strtoupper($journal->status) }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td>
            <td>: {{ \Carbon\Carbon::parse($journal->date)->format('d F Y') }}</td>
            <td class="label">Referensi</td>
            <td>: {{ $journal->reference_type }} {{ $journal->reference_id ? ' - '.$journal->reference_id : '' }}</td>
        </tr>
        <tr>
            <td class="label">Keterangan</td>
            <td colspan="3">: {{ $journal->notes ?? '-' }}</td>
        </tr>
    </table>

    <table class="lines-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="30%">Kode & Nama Akun</th>
                <th width="35%">Keterangan Baris</th>
                <th width="15%">Debit (Rp)</th>
                <th width="15%">Kredit (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($journal->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->account->code ?? '' }} - {{ $item->account->name ?? '' }}</td>
                <td>{{ $item->description }}</td>
                <td class="text-right">{{ number_format($item->debit, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->credit, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right font-bold">TOTAL KESELURUHAN</td>
                <td class="text-right font-bold">{{ number_format($journal->total_debit, 0, ',', '.') }}</td>
                <td class="text-right font-bold">{{ number_format($journal->total_credit, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <table class="signatures">
        <tr>
            <td>
                <div class="signature-line">Dibuat Oleh</div>
                <div style="font-size:10px; margin-top:5px;">{{ $journal->creator->name ?? '...........................' }}</div>
            </td>
            <td>
                <div class="signature-line">Diperiksa Oleh</div>
                <div style="font-size:10px; margin-top:5px;">(Penyelia Keuangan)</div>
            </td>
            <td>
                <div class="signature-line">Disetujui Oleh</div>
                <div style="font-size:10px; margin-top:5px;">(Manajer Keuangan)</div>
            </td>
        </tr>
    </table>

    <div style="font-size: 10px; margin-top: 30px; color: #666; text-align:right;">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
    </div>

</body>
</html>
