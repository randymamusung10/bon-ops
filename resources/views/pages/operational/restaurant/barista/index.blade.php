@extends('layouts.app')

@section('page_title', 'Barista Display System (BDS)')
@section('page_description', 'Pemantauan antrean pesanan minuman secara real-time untuk Bar Coffee & Beverage.')

@section('content')
<div class="container-fluid px-0">
    <!-- Grid of orders -->
    <div class="row g-4" id="bds-wrapper">
        @forelse($items->groupBy('pos_order_id') as $orderId => $orderItems)
            @php $firstItem = $orderItems->first(); $order = $firstItem->posOrder; @endphp
            <div class="col-xl-3 col-lg-4 col-md-6 order-card" data-order-id="{{ $orderId }}">
                <div class="card rounded-4 border-0 shadow-sm h-100 overflow-hidden" style="background: var(--bg-dark-secondary); border: 1px solid rgba(226, 232, 240, 0.1) !important;">
                    <!-- Header -->
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-start" 
                         style="background: color-mix(in srgb, var(--primary-accent) 5%, transparent); border-color: rgba(226, 232, 240, 0.1) !important;">
                        <div>
                            <span class="badge bg-primary-subtle text-primary mb-1 rounded-pill px-2.5 py-1" style="font-size: 10px; font-weight: 600;">{{ $order->order_number }}</span>
                            <h6 class="fw-bold mb-0 text-heading mt-1" style="font-family: 'Outfit', sans-serif; font-size: 14px;">
                                Meja: {{ $order->table_number ?? '-' }}
                            </h6>
                            <span class="text-muted" style="font-size: 11.5px;">Pelanggan: <strong>{{ $order->customer_name ?? 'Guest' }}</strong></span>
                        </div>
                        <span class="text-danger fw-semibold elapsed-timer badge bg-danger-subtle text-danger px-2.5 py-1 rounded-pill" data-start="{{ $order->created_at->timestamp }}" style="font-size: 11px;"><i class="bi bi-clock me-1"></i>0m</span>
                    </div>

                    <!-- Items List -->
                    <div class="p-3 flex-grow-1">
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                            @foreach($orderItems as $item)
                                <li class="pb-2 border-bottom border-dashed" style="border-color: rgba(226, 232, 240, 0.1) !important;">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="fw-bold text-heading" style="font-size: 13.5px;">
                                            {{ (float)$item->qty }}x {{ $item->product->name }}
                                        </div>
                                        <div>
                                            @if($item->status === 'pending')
                                                <span class="badge bg-warning-subtle text-warning px-2 py-0.5 rounded-pill" style="font-size: 10px;">Pending</span>
                                            @elseif($item->status === 'cooking')
                                                <span class="badge bg-info-subtle text-info px-2 py-0.5 rounded-pill" style="font-size: 10px;">Brewing</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($item->notes)
                                        <div class="p-2 rounded-3 mt-1.5" style="background: rgba(239, 68, 68, 0.05); border: 1px dashed rgba(239, 68, 68, 0.2);">
                                            <small class="text-danger d-block" style="font-size: 11px;"><i class="bi bi-chat-left-dots me-1"></i>Catatan: {{ $item->notes }}</small>
                                        </div>
                                    @endif

                                    <!-- Individual Action -->
                                    <div class="mt-2 text-end">
                                        @if($item->status === 'pending')
                                            <x-button type="button" variant="warning" size="sm" class="btn-status-update px-3 py-1 rounded-pill" 
                                                    data-id="{{ $item->id }}" data-status="cooking" style="font-size: 10px;">
                                                Mulai Racik
                                            </x-button>
                                        @elseif($item->status === 'cooking')
                                            <x-button type="button" variant="success" size="sm" class="btn-status-update px-3 py-1 rounded-pill" 
                                                    data-id="{{ $item->id }}" data-status="completed" style="font-size: 10px;">
                                                <i class="bi bi-check-lg me-1"></i> Selesai
                                            </x-button>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="text-muted mb-3"><i class="bi bi-cup-hot" style="font-size: 48px; color: var(--primary-accent);"></i></div>
                <h5 class="fw-bold text-heading" style="font-family: 'Outfit', sans-serif;">Tidak Ada Pesanan Barista</h5>
                <p class="text-muted" style="font-size: 13px;">Semua pesanan minuman telah selesai disajikan.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Timer
    setInterval(function() {
        $('.elapsed-timer').each(function() {
            var start = parseInt($(this).data('start'));
            var now = Math.floor(Date.now() / 1000);
            var diff = now - start;
            var minutes = Math.floor(diff / 60);
            $(this).html('<i class="bi bi-clock me-1"></i>' + minutes + 'm');
        });
    }, 10000);

    // Initial run
    $('.elapsed-timer').each(function() {
        var start = parseInt($(this).data('start'));
        var now = Math.floor(Date.now() / 1000);
        var diff = now - start;
        var minutes = Math.floor(diff / 60);
        $(this).html('<i class="bi bi-clock me-1"></i>' + minutes + 'm');
    });

    // Handle Status Updates
    $(document).on('click', '.btn-status-update', function() {
        var button = $(this);
        var id = button.data('id');
        var status = button.data('status');

        button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: `/operational/restaurant/barista/${id}/status`,
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                status: status
            },
            success: function(res) {
                if (res.success) {
                    AppAlert.success('Tersimpan!', res.message);
                    setTimeout(() => { location.reload(); }, 1000);
                }
            },
            error: function(xhr) {
                button.prop('disabled', false).html('Coba Lagi');
                AppAlert.error('Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan sistem.');
            }
        });
    });
});
</script>
@endpush