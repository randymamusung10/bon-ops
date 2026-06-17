@props([
    'icon' => 'bi-inbox',
    'title' => 'Tidak ada data',
    'description' => 'Data yang Anda cari tidak ditemukan atau masih kosong.',
    'actionText' => null,
    'actionUrl' => '#'
])

<div class="d-flex flex-column align-items-center justify-content-center py-5 text-center">
    <div class="mb-3" style="width: 64px; height: 64px; border-radius: 50%; background: color-mix(in srgb, var(--primary-accent) 10%, transparent); display: flex; align-items: center; justify-content: center;">
        <i class="bi {{ $icon }}" style="font-size: 28px; color: var(--primary-accent);"></i>
    </div>
    <h6 class="fw-bold mb-1" style="color: var(--text-heading); font-family: 'Outfit', sans-serif;">{{ $title }}</h6>
    <p class="mb-4" style="color: var(--text-muted); font-size: 13px; max-width: 300px;">{{ $description }}</p>
    
    @if($actionText)
        <a href="{{ $actionUrl }}" class="btn btn-primary custom-btn rounded-pill px-4" style="font-size: 13px; font-weight: 600;">
            {{ $actionText }}
        </a>
    @endif
</div>
