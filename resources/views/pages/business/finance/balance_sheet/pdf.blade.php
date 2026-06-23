<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Neraca Keuangan</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #1f2937; margin: 0; padding: 15px; }
        .document-header { width: 100%; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; margin-bottom: 15px; text-align: center; }
        .document-header h1 { font-size: 16px; margin: 0 0 4px 0; color: #111827; text-transform: uppercase; letter-spacing: 0.5px; }
        .document-header p { margin: 0; font-size: 10px; color: #4b5563; }
        .document-header h2 { font-size: 13px; margin: 8px 0 4px 0; color: #111827; text-transform: uppercase; }
        
        .layout-table { width: 100%; table-layout: fixed; border-collapse: collapse; }
        .layout-table > tbody > tr > td { vertical-align: top; padding: 0 10px; }
        .layout-left { border-right: 1px solid #e5e7eb; }
        
        .table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 10px; }
        .table th, .table td { padding: 4px 6px; text-align: left; }
        .border-bottom td { border-bottom: 1px solid #e5e7eb; }
        .border-top td { border-top: 1px solid #e5e7eb; }
        .border-top-double td { border-top: 2px solid #9ca3af; border-bottom: 3px double #9ca3af; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .fw-semibold { font-weight: 600; }
        
        .text-primary { color: #2563eb; }
        .text-danger { color: #dc2626; }
        .text-warning { color: #d97706; }
        .text-success { color: #16a34a; }
        .text-muted { color: #6b7280; }
        
        .ps-4 { padding-left: 15px !important; }
        .ps-5 { padding-left: 30px !important; }
        
        .summary-box { background-color: #f8fafc; padding: 8px 10px; border: 1px solid #e5e7eb; margin-top: 10px; font-size: 11px; }
    </style>
</head>
<body>

    <div class="document-header">
        <h1 style="margin-bottom: 10px;">{{ Auth::user()->tenant->name ?? 'BONOPS CORP' }}</h1>
        <h2>Laporan Neraca Keuangan</h2>
        <p>Posisi per tanggal {{ \Carbon\Carbon::parse($asOfDate)->isoFormat('D MMMM Y') }}</p>
        <p style="font-style: italic; margin-top: 3px;">(Disajikan dalam Rupiah, kecuali dinyatakan lain)</p>
    </div>

    <table class="layout-table">
        <tbody>
            <tr>
                <!-- ASET (KIRI) -->
                <td class="layout-left" width="50%">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td colspan="3" class="fw-bold text-success" style="font-size: 12px; padding-top: 5px;">Aset</td>
                            </tr>
                            @forelse($assets as $groupName => $accounts)
                                <tr>
                                    <td colspan="3" class="fw-semibold text-muted ps-4" style="padding-top: 6px;">{{ $groupName }}</td>
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
                                    <td colspan="3" class="text-center text-muted">Tidak ada data Aset.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>

                <!-- KEWAJIBAN & EKUITAS (KANAN) -->
                <td width="50%">
                    <table class="table">
                        <tbody>
                            <!-- KEWAJIBAN -->
                            <tr>
                                <td colspan="3" class="fw-bold text-danger" style="font-size: 12px; padding-top: 5px;">Kewajiban</td>
                            </tr>
                            @forelse($liabilities as $groupName => $accounts)
                                <tr>
                                    <td colspan="3" class="fw-semibold text-muted ps-4" style="padding-top: 6px;">{{ $groupName }}</td>
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
                                    <td colspan="3" class="text-center text-muted">Tidak ada data Kewajiban.</td>
                                </tr>
                            @endforelse
                            <tr class="border-top" style="background-color: #f9fafb;">
                                <td colspan="2" class="fw-bold text-right" style="padding: 6px;">Total Kewajiban</td>
                                <td class="fw-bold text-right" style="padding: 6px;">{{ number_format($totalLiability, 2, ',', '.') }}</td>
                            </tr>
                            <tr><td colspan="3" style="height: 10px;"></td></tr>

                            <!-- EKUITAS -->
                            <tr>
                                <td colspan="3" class="fw-bold text-primary" style="font-size: 12px; padding-top: 5px;">Ekuitas</td>
                            </tr>
                            @forelse($equities as $groupName => $accounts)
                                <tr>
                                    <td colspan="3" class="fw-semibold text-muted ps-4" style="padding-top: 6px;">{{ $groupName }}</td>
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
                                    <td colspan="3" class="text-center text-muted">Tidak ada data Ekuitas awal.</td>
                                </tr>
                            @endforelse
                            
                            <!-- LABA BERJALAN -->
                            <tr class="border-bottom">
                                <td class="ps-5 text-muted" width="20%">(AUTO)</td>
                                <td width="50%">Laba Tahun Berjalan</td>
                                <td class="text-right fw-semibold {{ $currentEarnings >= 0 ? 'text-success' : 'text-danger' }}" width="30%">{{ number_format($currentEarnings, 2, ',', '.') }}</td>
                            </tr>

                            <tr class="border-top" style="background-color: #f9fafb;">
                                <td colspan="2" class="fw-bold text-right" style="padding: 6px;">Total Ekuitas</td>
                                <td class="fw-bold text-right" style="padding: 6px;">{{ number_format($totalEquity + $currentEarnings, 2, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            
            <!-- GRAND TOTALS -->
            <tr>
                <td class="layout-left">
                    <div class="summary-box" style="background-color: #ecfdf5; border-color: #10b981;">
                        <table width="100%">
                            <tr>
                                <td><strong>Total Aset</strong></td>
                                <td class="text-right text-success"><strong>Rp {{ number_format($totalAsset, 2, ',', '.') }}</strong></td>
                            </tr>
                        </table>
                    </div>
                </td>
                <td>
                    <div class="summary-box" style="background-color: #eff6ff; border-color: #3b82f6;">
                        <table width="100%">
                            <tr>
                                <td><strong>Total Kewajiban & Ekuitas</strong></td>
                                <td class="text-right text-primary"><strong>Rp {{ number_format($totalEquityAndLiabilities, 2, ',', '.') }}</strong></td>
                            </tr>
                        </table>
                    </div>
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
