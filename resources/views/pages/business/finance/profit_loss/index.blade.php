@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h1 class="h4 fw-bold mb-1" style="color: var(--text-heading); font-family: 'Outfit', sans-serif; letter-spacing: -0.5px;">Laporan Laba Rugi</h1>
            <p class="mb-0" style="color: var(--text-light); font-size: 13.5px;">Pantau performa pendapatan dan beban perusahaan Anda.</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card rounded-4 p-4 mb-4 border-0 shadow-sm" style="background: var(--bg-dark-secondary);">
        <form action="{{ route('business.finance.profit_loss') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <x-form.label>Tanggal Mulai</x-form.label>
                    <x-form.input type="date" name="start_date" id="start_date" value="{{ $startDate }}" />
                </div>
                <div class="col-lg-4 col-md-6">
                    <x-form.label>Tanggal Akhir</x-form.label>
                    <x-form.input type="date" name="end_date" id="end_date" value="{{ $endDate }}" />
                </div>
                <div class="col-lg-4 col-md-12 d-flex gap-2 mt-md-3 mt-lg-0">
                    <x-button type="submit" variant="primary" icon="bi-filter">Terapkan Filter</x-button>
                    <x-button type="button" variant="light" icon="bi-printer" onclick="window.print()">Cetak Dokumen</x-button>
                </div>
            </div>
        </form>
    </div>

    <!-- Document Card -->
    <div class="card rounded-4 border-0 shadow-sm mb-4 printable-document" style="background: #ffffff;">
        <div class="card-body p-4 p-md-5">
            
            <!-- Document Header -->
            <div class="text-center mb-5 pb-4 border-bottom" style="border-color: rgba(226, 232, 240, 0.8) !important;">
                <h4 class="fw-bold mb-1" style="color: var(--text-heading); font-family: 'Outfit', sans-serif; font-size: 20px; text-transform: uppercase; letter-spacing: 1px;">{{ Auth::user()->tenant->name ?? 'BONOPS CORP' }}</h4>
                <p class="text-muted mb-3" style="font-size: 13px;">
                    @if(Auth::user()->tenant && Auth::user()->tenant->companies->count() > 0)
                        {{ Auth::user()->tenant->companies->first()->address ?? 'Alamat Perusahaan' }}<br>
                        Telp: {{ Auth::user()->tenant->companies->first()->phone ?? '-' }}
                    @else
                        Sistem Informasi Manajemen BonOps
                    @endif
                </p>
                <h5 class="fw-bold mb-1" style="color: var(--text-heading); font-size: 16px; text-transform: uppercase;">Laporan Laba Rugi</h5>
                <p class="text-muted mb-0" style="font-size: 13.5px;">Periode: {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMMM Y') }} - {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMMM Y') }}</p>
                <p class="text-muted mb-0 mt-1" style="font-size: 12px; font-style: italic;">(Disajikan dalam Rupiah, kecuali dinyatakan lain)</p>
            </div>

            <!-- Content Table -->
            <div class="table-responsive">
                <table class="table table-borderless align-middle w-100" style="font-size: 14px; font-family: 'Inter', sans-serif;">
                    <tbody>
                        <!-- PENDAPATAN -->
                        <tr>
                            <td colspan="3" class="fw-bold text-primary pb-2" style="font-size: 15px; letter-spacing: 0.2px;">Pendapatan</td>
                        </tr>
                        @forelse($revenues as $groupName => $accounts)
                            <tr>
                                <td colspan="3" class="fw-semibold text-muted ps-4 pt-3 pb-2" style="font-size: 13.5px;">{{ $groupName }}</td>
                            </tr>
                            @foreach($accounts as $acc)
                            <tr style="border-bottom: 1px solid rgba(226, 232, 240, 0.3);">
                                <td class="ps-5 text-muted" style="width: 15%;">{{ $acc['code'] }}</td>
                                <td class="ps-2" style="width: 55%;">{{ $acc['name'] }}</td>
                                <td class="text-end pe-3" style="width: 30%;">{{ number_format($acc['balance'], 2, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">Tidak ada transaksi pendapatan.</td>
                            </tr>
                        @endforelse
                        <!-- TOTAL PENDAPATAN -->
                        <tr style="border-bottom: 1px solid rgba(226, 232, 240, 0.8);">
                            <td colspan="2" class="fw-bold text-end pe-4 pt-3 pb-3">Total Pendapatan</td>
                            <td class="fw-bold text-end pe-3 pt-3 pb-3" style="border-top: 1px solid rgba(226, 232, 240, 0.8);">
                                {{ number_format($totalRevenue, 2, ',', '.') }}
                            </td>
                        </tr>
                        <tr><td colspan="3" style="height: 15px;"></td></tr>

                        <!-- HPP -->
                        <tr>
                            <td colspan="3" class="fw-bold text-danger pb-2" style="font-size: 15px; letter-spacing: 0.2px;">Harga Pokok Penjualan</td>
                        </tr>
                        @forelse($cogs as $groupName => $accounts)
                            <tr>
                                <td colspan="3" class="fw-semibold text-muted ps-4 pt-3 pb-2" style="font-size: 13.5px;">{{ $groupName }}</td>
                            </tr>
                            @foreach($accounts as $acc)
                            <tr style="border-bottom: 1px solid rgba(226, 232, 240, 0.3);">
                                <td class="ps-5 text-muted" style="width: 15%;">{{ $acc['code'] }}</td>
                                <td class="ps-2" style="width: 55%;">{{ $acc['name'] }}</td>
                                <td class="text-end pe-3 text-danger" style="width: 30%;">({{ number_format($acc['balance'], 2, ',', '.') }})</td>
                            </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">Tidak ada transaksi Harga Pokok Penjualan.</td>
                            </tr>
                        @endforelse
                        <!-- TOTAL HPP -->
                        <tr style="border-bottom: 1px solid rgba(226, 232, 240, 0.8);">
                            <td colspan="2" class="fw-bold text-end pe-4 pt-3 pb-3">Total Harga Pokok Penjualan</td>
                            <td class="fw-bold text-end pe-3 pt-3 pb-3 text-danger" style="border-top: 1px solid rgba(226, 232, 240, 0.8);">
                                ({{ number_format($totalCogs, 2, ',', '.') }})
                            </td>
                        </tr>

                        <!-- LABA KOTOR -->
                        <tr style="background-color: rgba(248, 250, 252, 0.8);">
                            <td colspan="2" class="fw-bold text-end pe-4 py-3" style="font-size: 15px;">Laba Kotor</td>
                            <td class="fw-bold text-end pe-3 py-3 {{ $grossProfit >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 15px; border-top: 2px solid rgba(226, 232, 240, 1); border-bottom: 2px solid rgba(226, 232, 240, 1);">
                                {{ number_format($grossProfit, 2, ',', '.') }}
                            </td>
                        </tr>
                        <tr><td colspan="3" style="height: 20px;"></td></tr>

                        <!-- BEBAN OPERASIONAL -->
                        <tr>
                            <td colspan="3" class="fw-bold text-warning pb-2" style="font-size: 15px; letter-spacing: 0.2px;">Beban Operasional</td>
                        </tr>
                        @forelse($expenses as $groupName => $accounts)
                            <tr>
                                <td colspan="3" class="fw-semibold text-muted ps-4 pt-3 pb-2" style="font-size: 13.5px;">{{ $groupName }}</td>
                            </tr>
                            @foreach($accounts as $acc)
                            <tr style="border-bottom: 1px solid rgba(226, 232, 240, 0.3);">
                                <td class="ps-5 text-muted" style="width: 15%;">{{ $acc['code'] }}</td>
                                <td class="ps-2" style="width: 55%;">{{ $acc['name'] }}</td>
                                <td class="text-end pe-3 text-warning" style="width: 30%;">({{ number_format($acc['balance'], 2, ',', '.') }})</td>
                            </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">Tidak ada transaksi Beban Operasional.</td>
                            </tr>
                        @endforelse
                        <!-- TOTAL BEBAN -->
                        <tr style="border-bottom: 1px solid rgba(226, 232, 240, 0.8);">
                            <td colspan="2" class="fw-bold text-end pe-4 pt-3 pb-3">Total Beban Operasional</td>
                            <td class="fw-bold text-end pe-3 pt-3 pb-3 text-warning" style="border-top: 1px solid rgba(226, 232, 240, 0.8);">
                                ({{ number_format($totalExpense, 2, ',', '.') }})
                            </td>
                        </tr>

                        <!-- LABA BERSIH -->
                        <tr style="background-color: color-mix(in srgb, {{ $netProfit >= 0 ? '#10b981' : '#ef4444' }} 5%, transparent);">
                            <td colspan="2" class="fw-bold text-end pe-4 py-4" style="font-size: 16px;">Laba Bersih</td>
                            <td class="fw-bold text-end pe-3 py-4 {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 16px; border-top: 2px solid rgba(226, 232, 240, 1); border-bottom: 4px double rgba(226, 232, 240, 1);">
                                Rp {{ number_format($netProfit, 2, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<style>
    @media print {
        body * { visibility: hidden; }
        .printable-document, .printable-document * { visibility: visible; }
        .printable-document { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none !important; }
        .table-borderless td { padding-top: 4px; padding-bottom: 4px; }
    }
    .table-borderless td { padding-top: 0.6rem; padding-bottom: 0.6rem; }
    .table-borderless tbody tr:hover td { background-color: rgba(248, 250, 252, 0.5); }
</style>
@endsection