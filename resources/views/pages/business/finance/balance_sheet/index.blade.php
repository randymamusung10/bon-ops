@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h1 class="h4 fw-bold mb-1" style="color: var(--text-heading); font-family: 'Outfit', sans-serif; letter-spacing: -0.5px;">Neraca Keuangan</h1>
            <p class="mb-0" style="color: var(--text-light); font-size: 13.5px;">Ringkasan posisi keuangan: Aset, Kewajiban, dan Ekuitas perusahaan.</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card rounded-4 p-4 mb-4 border-0 shadow-sm" style="background: var(--bg-dark-secondary);">
        <form action="{{ route('business.finance.balance_sheet') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <x-form.label>Per Tanggal (As of Date)</x-form.label>
                    <x-form.input type="date" name="as_of_date" id="as_of_date" value="{{ $asOfDate }}" />
                </div>
                <div class="col-lg-4 col-md-12 d-flex gap-2 mt-md-3 mt-lg-0">
                    <x-button type="submit" variant="primary" icon="bi-filter">Terapkan Filter</x-button>
                    <x-button type="button" variant="light" icon="bi-file-earmark-pdf" onclick="window.location.href='{{ route('business.finance.balance_sheet.export_pdf', ['as_of_date' => $asOfDate]) }}'">Export PDF</x-button>
                </div>
            </div>
        </form>
    </div>

    <!-- Document Card -->
    <div class="card rounded-4 border-0 shadow-sm mb-4 printable-document" style="background: #ffffff;">
        <div class="card-body p-4 p-md-5">
            
            <!-- Document Header -->
            <div class="text-center mb-5 pb-4 border-bottom" style="border-color: rgba(226, 232, 240, 0.8) !important;">
                <h4 class="fw-bold mb-3" style="color: var(--text-heading); font-family: 'Outfit', sans-serif; font-size: 20px; text-transform: uppercase; letter-spacing: 1px;">{{ Auth::user()->tenant->name ?? 'BONOPS CORP' }}</h4>
                <h5 class="fw-bold mb-1" style="color: var(--text-heading); font-size: 16px; text-transform: uppercase;">Laporan Neraca Keuangan</h5>
                <p class="text-muted mb-0" style="font-size: 13.5px;">Posisi per tanggal {{ \Carbon\Carbon::parse($asOfDate)->isoFormat('D MMMM Y') }}</p>
                <p class="text-muted mb-0 mt-1" style="font-size: 12px; font-style: italic;">(Disajikan dalam Rupiah, kecuali dinyatakan lain)</p>
            </div>

            <!-- Content Table -->
            <div class="row g-0">
                <!-- ASET (HARTA) -->
                <div class="col-md-6 pe-md-4" style="border-right: 1px solid rgba(226, 232, 240, 0.8);">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle w-100" style="font-size: 14px; font-family: 'Inter', sans-serif;">
                            <tbody>
                                <tr>
                                    <td colspan="3" class="fw-bold text-success pb-2" style="font-size: 15px; letter-spacing: 0.2px;">Aset</td>
                                </tr>
                                @forelse($assets as $groupName => $accounts)
                                    <tr>
                                        <td colspan="3" class="fw-semibold text-muted ps-4 pt-3 pb-2" style="font-size: 13.5px;">{{ $groupName }}</td>
                                    </tr>
                                    @foreach($accounts as $acc)
                                    <tr style="border-bottom: 1px solid rgba(226, 232, 240, 0.3);">
                                        <td class="ps-5 text-muted" style="width: 20%;">{{ $acc['code'] }}</td>
                                        <td class="ps-2" style="width: 50%;">{{ $acc['name'] }}</td>
                                        <td class="text-end pe-3" style="width: 30%;">{{ number_format($acc['balance'], 2, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Tidak ada data Aset.</td>
                                    </tr>
                                @endforelse
                                <tr><td colspan="3" style="height: 30px;"></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- KEWAJIBAN & EKUITAS -->
                <div class="col-md-6 ps-md-4 mt-4 mt-md-0">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle w-100" style="font-size: 14px; font-family: 'Inter', sans-serif;">
                            <tbody>
                                <!-- KEWAJIBAN -->
                                <tr>
                                    <td colspan="3" class="fw-bold text-danger pb-2" style="font-size: 15px; letter-spacing: 0.2px;">Kewajiban</td>
                                </tr>
                                @forelse($liabilities as $groupName => $accounts)
                                    <tr>
                                        <td colspan="3" class="fw-semibold text-muted ps-4 pt-3 pb-2" style="font-size: 13.5px;">{{ $groupName }}</td>
                                    </tr>
                                    @foreach($accounts as $acc)
                                    <tr style="border-bottom: 1px solid rgba(226, 232, 240, 0.3);">
                                        <td class="ps-5 text-muted" style="width: 20%;">{{ $acc['code'] }}</td>
                                        <td class="ps-2" style="width: 50%;">{{ $acc['name'] }}</td>
                                        <td class="text-end pe-3" style="width: 30%;">{{ number_format($acc['balance'], 2, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Tidak ada data Kewajiban.</td>
                                    </tr>
                                @endforelse
                                <!-- TOTAL KEWAJIBAN -->
                                <tr style="border-bottom: 1px solid rgba(226, 232, 240, 0.8);">
                                    <td colspan="2" class="fw-bold text-end pe-4 pt-3 pb-3">Total Kewajiban</td>
                                    <td class="fw-bold text-end pe-3 pt-3 pb-3" style="border-top: 1px solid rgba(226, 232, 240, 0.8);">
                                        {{ number_format($totalLiability, 2, ',', '.') }}
                                    </td>
                                </tr>
                                <tr><td colspan="3" style="height: 15px;"></td></tr>

                                <!-- EKUITAS -->
                                <tr>
                                    <td colspan="3" class="fw-bold text-primary pb-2" style="font-size: 15px; letter-spacing: 0.2px;">Ekuitas</td>
                                </tr>
                                @forelse($equities as $groupName => $accounts)
                                    <tr>
                                        <td colspan="3" class="fw-semibold text-muted ps-4 pt-3 pb-2" style="font-size: 13.5px;">{{ $groupName }}</td>
                                    </tr>
                                    @foreach($accounts as $acc)
                                    <tr style="border-bottom: 1px solid rgba(226, 232, 240, 0.3);">
                                        <td class="ps-5 text-muted" style="width: 20%;">{{ $acc['code'] }}</td>
                                        <td class="ps-2" style="width: 50%;">{{ $acc['name'] }}</td>
                                        <td class="text-end pe-3" style="width: 30%;">{{ number_format($acc['balance'], 2, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-2">Tidak ada data Ekuitas awal.</td>
                                    </tr>
                                @endforelse
                                
                                <!-- LABA BERJALAN -->
                                <tr style="border-bottom: 1px solid rgba(226, 232, 240, 0.3);">
                                    <td class="ps-5 text-muted" style="width: 20%; font-size: 11px;">(AUTO)</td>
                                    <td class="ps-2" style="width: 50%;">Laba Tahun Berjalan</td>
                                    <td class="text-end pe-3 {{ $currentEarnings >= 0 ? 'text-success' : 'text-danger' }} fw-semibold" style="width: 30%;">{{ number_format($currentEarnings, 2, ',', '.') }}</td>
                                </tr>

                                <!-- TOTAL EKUITAS -->
                                <tr style="border-bottom: 1px solid rgba(226, 232, 240, 0.8);">
                                    <td colspan="2" class="fw-bold text-end pe-4 pt-3 pb-3">Total Ekuitas</td>
                                    <td class="fw-bold text-end pe-3 pt-3 pb-3" style="border-top: 1px solid rgba(226, 232, 240, 0.8);">
                                        {{ number_format($totalEquity + $currentEarnings, 2, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Grand Totals Container -->
            <div class="row g-0 mt-3 pt-4 border-top" style="border-color: rgba(226, 232, 240, 1) !important;">
                <div class="col-md-6 pe-md-4">
                    <div class="d-flex justify-content-between align-items-center" style="background-color: color-mix(in srgb, #10b981 5%, transparent); padding: 12px 16px; border-top: 2px solid rgba(226, 232, 240, 1); border-bottom: 4px double rgba(226, 232, 240, 1);">
                        <span class="fw-semibold" style="font-size: 15px;">Total Aset</span>
                        <span class="fw-bold text-success" style="font-size: 16px;">Rp {{ number_format($totalAsset, 2, ',', '.') }}</span>
                    </div>
                </div>
                <div class="col-md-6 ps-md-4 mt-3 mt-md-0">
                    <div class="d-flex justify-content-between align-items-center" style="background-color: color-mix(in srgb, #3b82f6 5%, transparent); padding: 12px 16px; border-top: 2px solid rgba(226, 232, 240, 1); border-bottom: 4px double rgba(226, 232, 240, 1);">
                        <span class="fw-semibold" style="font-size: 15px;">Total Kewajiban & Ekuitas</span>
                        <span class="fw-bold text-primary" style="font-size: 16px;">Rp {{ number_format($totalEquityAndLiabilities, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Validation Check -->
            @if(round($totalAsset, 2) === round($totalEquityAndLiabilities, 2))
                <div class="alert alert-success mt-5 d-flex align-items-center mb-0 border-0 shadow-sm hide-on-print">
                    <i class="bi bi-check-circle-fill me-3 fs-4"></i>
                    <div>
                        <h6 class="fw-semibold mb-1">Neraca Seimbang (Balanced)</h6>
                        <span style="font-size: 13.5px;">Total Aset sama dengan Total Kewajiban dan Ekuitas. Kondisi keuangan tercatat dengan valid.</span>
                    </div>
                </div>
            @else
                <div class="alert alert-danger mt-5 d-flex align-items-center mb-0 border-0 shadow-sm hide-on-print">
                    <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                    <div>
                        <h6 class="fw-semibold mb-1">Neraca Tidak Seimbang (Unbalanced)</h6>
                        <span style="font-size: 13.5px;">Terdapat selisih sebesar Rp {{ number_format(abs($totalAsset - $totalEquityAndLiabilities), 2, ',', '.') }}. Periksa kembali keseimbangan input Jurnal Umum.</span>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

<style>
    @media print {
        body * { visibility: hidden; }
        .printable-document, .printable-document * { visibility: visible; }
        .printable-document { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none !important; border: none !important; }
        .hide-on-print { display: none !important; }
        .table-borderless td { padding-top: 4px; padding-bottom: 4px; }
    }
    .table-borderless td { padding-top: 0.6rem; padding-bottom: 0.6rem; }
    .table-borderless tbody tr:hover td { background-color: rgba(248, 250, 252, 0.5); }
</style>
@endsection