@extends('layouts.app')

@section('page_title', 'Executive Dashboard')
@section('page_description')
Ringkasan performa bisnis dan operasional. Periode: <span class="fw-medium text-heading">{{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</span>
@endsection

@section('page_actions')
<form id="filter-form" action="{{ route('business.reports.executive') }}" method="GET" class="d-flex align-items-center gap-2">
    <x-form.input type="date" name="start_date" id="start_date" value="{{ $startDate->format('Y-m-d') }}" style="max-width: 140px; font-size: 13px;" />
    <span class="text-muted">-</span>
    <x-form.input type="date" name="end_date" id="end_date" value="{{ $endDate->format('Y-m-d') }}" style="max-width: 140px; font-size: 13px;" />
    <x-button type="submit" variant="primary" icon="bi-filter" style="padding: 0.375rem 0.75rem; font-size: 13px;">Filter</x-button>
</form>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="row g-3 mb-4">
        <!-- Sales Card -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card rounded-4 border-0 shadow-sm p-3 h-100" style="background: var(--bg-dark-secondary);">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0 text-muted fw-medium" style="font-size: 13px;">Total Penjualan</h6>
                    <div class="icon-box rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(16, 185, 129, 0.12); color: #10b981;">
                        <i class="bi bi-graph-up-arrow fs-6"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-2 text-heading">Rp {{ number_format($salesThisPeriod, 0, ',', '.') }}</h4>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge {{ $salesGrowth >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill px-2 py-1" style="font-size: 11px;">
                        <i class="bi {{ $salesGrowth >= 0 ? 'bi-arrow-up-short' : 'bi-arrow-down-short' }}"></i>
                        {{ number_format(abs($salesGrowth), 1) }}%
                    </span>
                    <span class="text-muted" style="font-size: 11px;">vs periode sebelumnya</span>
                </div>
            </div>
        </div>

        <!-- Net Profit Card -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card rounded-4 border-0 shadow-sm p-3 h-100" style="background: var(--bg-dark-secondary);">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0 text-muted fw-medium" style="font-size: 13px;">Keuntungan Kotor</h6>
                    <div class="icon-box rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(59, 130, 246, 0.12); color: #3b82f6;">
                        <i class="bi bi-wallet2 fs-6"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-2 text-heading">Rp {{ number_format($netProfitThisPeriod, 0, ',', '.') }}</h4>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted" style="font-size: 11px;">Omset dikurangi HPP (Sesuai Filter)</span>
                </div>
            </div>
        </div>

        <!-- Expenses Card -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card rounded-4 border-0 shadow-sm p-3 h-100" style="background: var(--bg-dark-secondary);">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0 text-muted fw-medium" style="font-size: 13px;">Total Pengeluaran</h6>
                    <div class="icon-box rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                        <i class="bi bi-graph-down-arrow fs-6"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-2 text-heading">Rp {{ number_format($expensesThisPeriod, 0, ',', '.') }}</h4>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge {{ $expensesGrowth <= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill px-2 py-1" style="font-size: 11px;">
                        <i class="bi {{ $expensesGrowth >= 0 ? 'bi-arrow-up-short' : 'bi-arrow-down-short' }}"></i>
                        {{ number_format(abs($expensesGrowth), 1) }}%
                    </span>
                    <span class="text-muted" style="font-size: 11px;">vs periode sebelumnya</span>
                </div>
            </div>
        </div>

        <!-- Transaction Card -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card rounded-4 border-0 shadow-sm p-3 h-100" style="background: var(--bg-dark-secondary);">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0 text-muted fw-medium" style="font-size: 13px;">Transaksi POS</h6>
                    <div class="icon-box rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(14, 165, 233, 0.12); color: #0ea5e9;">
                        <i class="bi bi-receipt fs-6"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-2 text-heading">{{ number_format($transactionsCount, 0, ',', '.') }} <span class="fs-6 text-muted fw-normal">Trx</span></h4>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted" style="font-size: 11px;">Total nota (Sesuai Filter)</span>
                </div>
            </div>
        </div>
        
        <!-- Items Sold Card (NEW) -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card rounded-4 border-0 shadow-sm p-3 h-100" style="background: var(--bg-dark-secondary);">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0 text-muted fw-medium" style="font-size: 13px;">Item Terjual</h6>
                    <div class="icon-box rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(244, 63, 94, 0.12); color: #f43f5e;">
                        <i class="bi bi-cart-check fs-6"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-2 text-heading">{{ number_format($itemsSoldThisPeriod, 0, ',', '.') }} <span class="fs-6 text-muted fw-normal">Pcs</span></h4>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted" style="font-size: 11px;">Total barang lunas (Sesuai Filter)</span>
                </div>
            </div>
        </div>

        <!-- Average Transaction Value Card -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card rounded-4 border-0 shadow-sm p-3 h-100" style="background: var(--bg-dark-secondary);">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0 text-muted fw-medium" style="font-size: 13px;">Rata-rata Transaksi</h6>
                    <div class="icon-box rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(99, 102, 241, 0.12); color: #6366f1;">
                        <i class="bi bi-calculator fs-6"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-2 text-heading">Rp {{ number_format($avgTransactionValue, 0, ',', '.') }}</h4>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted" style="font-size: 11px;">Belanja rata-rata per nota</span>
                </div>
            </div>
        </div>
        
        <!-- Unpaid Receivable Card -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card rounded-4 border-0 shadow-sm p-3 h-100" style="background: var(--bg-dark-secondary);">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0 text-muted fw-medium" style="font-size: 13px;">Piutang POS</h6>
                    <div class="icon-box rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                        <i class="bi bi-clock-history fs-6"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-2 text-heading">Rp {{ number_format($totalReceivable, 0, ',', '.') }}</h4>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted" style="font-size: 11px;">Total tagihan berjalan saat ini</span>
                </div>
            </div>
        </div>

        <!-- Stock Valuation Card -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card rounded-4 border-0 shadow-sm p-3 h-100" style="background: var(--bg-dark-secondary);">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0 text-muted fw-medium" style="font-size: 13px;">Valuasi Stok</h6>
                    <div class="icon-box rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(168, 85, 247, 0.12); color: #a855f7;">
                        <i class="bi bi-box-seam fs-6"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-2 text-heading">Rp {{ number_format($stockValuation, 0, ',', '.') }}</h4>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted" style="font-size: 11px;">Modal tertahan di gudang saat ini</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sales Trend Chart -->
        <div class="col-12 col-lg-8">
            <div class="card rounded-4 border-0 shadow-sm p-4 h-100" style="background: var(--bg-dark-secondary);">
                <h6 class="mb-4 fw-bold text-heading">Tren Penjualan (Berdasarkan Filter)</h6>
                <div id="salesTrendChart" style="min-height: 300px;"></div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="col-12 col-lg-4">
            <div class="card rounded-4 border-0 shadow-sm p-4 h-100" style="background: var(--bg-dark-secondary);">
                <h6 class="mb-4 fw-bold text-heading">Produk Terlaris (Berdasarkan Filter)</h6>
                <div class="table-responsive">
                    <table class="table align-middle mb-0" style="--bs-table-bg: transparent;">
                        <tbody style="font-size: 13px;">
                            @forelse($topProducts as $item)
                            <tr>
                                <td class="px-0 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="icon-box rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 32px; height: 32px;">
                                            <span class="fw-bold text-secondary">{{ $loop->iteration }}</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold text-heading" style="font-size: 13px;">{{ $item->name }}</h6>
                                            <span class="text-muted" style="font-size: 11px;">{{ number_format($item->total_qty, 0, ',', '.') }} Terjual</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-0 py-3 text-end fw-semibold text-success">
                                    Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center py-4 text-muted">Belum ada data penjualan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions Row -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card rounded-4 border-0 shadow-sm p-4" style="background: var(--bg-dark-secondary);">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="mb-0 fw-bold text-heading">Transaksi Terakhir (Berdasarkan Filter)</h6>
                    <a href="{{ route('business.reports.sales') }}" class="btn btn-sm btn-light rounded-pill px-3" style="font-size: 12px; font-weight: 500;">Lihat Semua Laporan <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 w-100" style="--bs-table-bg: transparent; --bs-table-border-color: rgba(226, 232, 240, 0.6);">
                        <thead style="background-color: color-mix(in srgb, var(--primary-accent) 4%, transparent);">
                            <tr style="font-size: 12px; color: var(--text-muted); letter-spacing: 0.2px;">
                                <th class="ps-3 py-3" style="width: 15%;">Waktu</th>
                                <th class="py-3" style="width: 20%;">Nomor Order</th>
                                <th class="py-3" style="width: 20%;">Kasir</th>
                                <th class="py-3 text-end" style="width: 20%;">Nominal (Rp)</th>
                                <th class="py-3 text-center" style="width: 15%;">Metode</th>
                                <th class="py-3 text-center" style="width: 10%;">Status</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 13px; color: var(--text-heading);">
                            @forelse($recentTransactions as $trx)
                            <tr>
                                <td class="ps-3 py-3 text-muted">{{ \Carbon\Carbon::parse($trx->created_at)->format('d M Y, H:i') }}</td>
                                <td class="py-3 fw-semibold text-heading">{{ $trx->order_number }}</td>
                                <td class="py-3">{{ $trx->creator ? $trx->creator->name : '-' }}</td>
                                <td class="py-3 text-end fw-semibold text-success">{{ number_format($trx->grand_total, 0, ',', '.') }}</td>
                                <td class="py-3 text-center text-uppercase">
                                    {{ $trx->payment_method ? $trx->payment_method : '-' }}
                                </td>
                                <td class="py-3 text-center">
                                    @if($trx->payment_status == 'paid')
                                        <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">Lunas</span>
                                    @elseif($trx->payment_status == 'partial')
                                        <span class="badge bg-warning-subtle text-warning px-2 py-1 rounded-pill">Parsial</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill">Belum Bayar</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada transaksi bulan ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var isDarkMode = document.documentElement.getAttribute('data-bs-theme') === 'dark';
    var textColor = isDarkMode ? '#94a3b8' : '#64748b';
    var gridColor = isDarkMode ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';

    var options = {
        series: [{
            name: 'Pendapatan',
            data: {!! json_encode($trendTotals) !!}
        }],
        chart: {
            height: 320,
            type: 'area',
            fontFamily: 'Inter, sans-serif',
            toolbar: {
                show: false
            },
            background: 'transparent'
        },
        colors: ['#10b981'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.05,
                stops: [0, 90, 100]
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        xaxis: {
            categories: {!! json_encode($trendDates) !!},
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false
            },
            labels: {
                style: {
                    colors: textColor,
                    fontSize: '11px'
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: textColor,
                    fontSize: '11px'
                },
                formatter: function (value) {
                    if(value >= 1000000) {
                        return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                    } else if (value >= 1000) {
                        return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                    }
                    return 'Rp ' + value;
                }
            }
        },
        grid: {
            borderColor: gridColor,
            strokeDashArray: 4,
            yaxis: {
                lines: {
                    show: true
                }
            }
        },
        theme: {
            mode: isDarkMode ? 'dark' : 'light'
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return "Rp " + new Intl.NumberFormat('id-ID').format(val)
                }
            }
        }
    };

    var chart = new ApexCharts(document.querySelector("#salesTrendChart"), options);
    chart.render();
    
    // Listen for theme changes if any
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === "data-bs-theme") {
                var newIsDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
                chart.updateOptions({
                    theme: {
                        mode: newIsDark ? 'dark' : 'light'
                    },
                    xaxis: {
                        labels: {
                            style: { colors: newIsDark ? '#94a3b8' : '#64748b' }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: { colors: newIsDark ? '#94a3b8' : '#64748b' }
                        }
                    },
                    grid: {
                        borderColor: newIsDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)'
                    }
                });
            }
        });
    });
    observer.observe(document.documentElement, { attributes: true });
});
</script>
@endpush