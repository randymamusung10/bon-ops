<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Laba Rugi</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #1f2937; margin: 0; padding: 20px; }
        .document-header { width: 100%; border-bottom: 2px solid #e5e7eb; padding-bottom: 15px; margin-bottom: 20px; text-align: center; }
        .document-header h1 { font-size: 18px; margin: 0 0 5px 0; color: #111827; text-transform: uppercase; letter-spacing: 0.5px; }
        .document-header p { margin: 0; font-size: 11px; color: #4b5563; }
        .document-header h2 { font-size: 14px; margin: 10px 0 5px 0; color: #111827; text-transform: uppercase; }
        
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 11px; }
        .table th, .table td { padding: 6px 10px; text-align: left; }
        .border-bottom td { border-bottom: 1px solid #e5e7eb; }
        .border-top td { border-top: 1px solid #e5e7eb; }
        .border-top-double td { border-top: 2px solid #9ca3af; border-bottom: 4px double #9ca3af; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .fw-semibold { font-weight: 600; }
        
        .text-primary { color: #2563eb; }
        .text-danger { color: #dc2626; }
        .text-warning { color: #d97706; }
        .text-success { color: #16a34a; }
        .text-muted { color: #6b7280; }
        
        .ps-4 { padding-left: 20px !important; }
        .ps-5 { padding-left: 40px !important; }
        .bg-light { background-color: #f8fafc; }
    </style>
</head>
<body>

    <div class="document-header">
        <h1 style="margin-bottom: 10px;">{{ Auth::user()->tenant->name ?? 'BONOPS CORP' }}</h1>
        <h2>Laporan Laba Rugi</h2>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMMM Y') }} - {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMMM Y') }}</p>
        <p style="font-style: italic; margin-top: 5px;">(Disajikan dalam Rupiah, kecuali dinyatakan lain)</p>
    </div>

    <table class="table">
        <tbody>
            <!-- PENDAPATAN -->
            <tr>
                <td colspan="3" class="fw-bold text-primary" style="font-size: 13px; padding-top: 10px;">Pendapatan</td>
            </tr>
            @forelse($revenues as $groupName => $accounts)
                <tr>
                    <td colspan="3" class="fw-semibold text-muted ps-4" style="padding-top: 8px;">{{ $groupName }}</td>
                </tr>
                @foreach($accounts as $acc)
                <tr class="border-bottom">
                    <td class="ps-5 text-muted" width="20%">{{ $acc['code'] }}</td>
                    <td width="50%">{{ $acc['name'] }}</td>
                    <td class="text-right" width="30%">{{ number_format($acc['balance'], 2, ',', '.') }}</td>
                </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="3" class="text-center text-muted">Tidak ada transaksi pendapatan.</td>
                </tr>
            @endforelse
            <tr class="border-top" style="background-color: #f9fafb;">
                <td colspan="2" class="fw-bold text-right" style="padding: 8px 10px;">Total Pendapatan</td>
                <td class="fw-bold text-right" style="padding: 8px 10px;">{{ number_format($totalRevenue, 2, ',', '.') }}</td>
            </tr>
            <tr><td colspan="3" style="height: 10px;"></td></tr>

            <!-- HPP -->
            <tr>
                <td colspan="3" class="fw-bold text-danger" style="font-size: 13px; padding-top: 10px;">Harga Pokok Penjualan</td>
            </tr>
            @forelse($cogs as $groupName => $accounts)
                <tr>
                    <td colspan="3" class="fw-semibold text-muted ps-4" style="padding-top: 8px;">{{ $groupName }}</td>
                </tr>
                @foreach($accounts as $acc)
                <tr class="border-bottom">
                    <td class="ps-5 text-muted" width="20%">{{ $acc['code'] }}</td>
                    <td width="50%">{{ $acc['name'] }}</td>
                    <td class="text-right text-danger" width="30%">({{ number_format($acc['balance'], 2, ',', '.') }})</td>
                </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="3" class="text-center text-muted">Tidak ada transaksi Harga Pokok Penjualan.</td>
                </tr>
            @endforelse
            <tr class="border-top" style="background-color: #f9fafb;">
                <td colspan="2" class="fw-bold text-right" style="padding: 8px 10px;">Total Harga Pokok Penjualan</td>
                <td class="fw-bold text-right text-danger" style="padding: 8px 10px;">({{ number_format($totalCogs, 2, ',', '.') }})</td>
            </tr>
            
            <!-- LABA KOTOR -->
            <tr class="bg-light" style="border-top: 2px solid #e5e7eb; border-bottom: 2px solid #e5e7eb;">
                <td colspan="2" class="fw-bold text-right" style="font-size: 12px; padding: 10px;">Laba Kotor</td>
                <td class="fw-bold text-right {{ $grossProfit >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 12px; padding: 10px;">
                    {{ number_format($grossProfit, 2, ',', '.') }}
                </td>
            </tr>
            <tr><td colspan="3" style="height: 15px;"></td></tr>

            <!-- BEBAN OPERASIONAL -->
            <tr>
                <td colspan="3" class="fw-bold text-warning" style="font-size: 13px; padding-top: 10px;">Beban Operasional</td>
            </tr>
            @forelse($expenses as $groupName => $accounts)
                <tr>
                    <td colspan="3" class="fw-semibold text-muted ps-4" style="padding-top: 8px;">{{ $groupName }}</td>
                </tr>
                @foreach($accounts as $acc)
                <tr class="border-bottom">
                    <td class="ps-5 text-muted" width="20%">{{ $acc['code'] }}</td>
                    <td width="50%">{{ $acc['name'] }}</td>
                    <td class="text-right text-warning" width="30%">({{ number_format($acc['balance'], 2, ',', '.') }})</td>
                </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="3" class="text-center text-muted">Tidak ada transaksi Beban Operasional.</td>
                </tr>
            @endforelse
            <tr class="border-top" style="background-color: #f9fafb;">
                <td colspan="2" class="fw-bold text-right" style="padding: 8px 10px;">Total Beban Operasional</td>
                <td class="fw-bold text-right text-warning" style="padding: 8px 10px;">({{ number_format($totalExpense, 2, ',', '.') }})</td>
            </tr>

            <!-- LABA BERSIH -->
            <tr class="border-top-double" style="background-color: {{ $netProfit >= 0 ? '#ecfdf5' : '#fef2f2' }};">
                <td colspan="2" class="fw-bold text-right" style="font-size: 13px; padding: 12px 10px;">Laba Bersih</td>
                <td class="fw-bold text-right {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 13px; padding: 12px 10px;">
                    Rp {{ number_format($netProfit, 2, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

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
