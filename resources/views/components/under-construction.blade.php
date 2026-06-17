<div class="card rounded-4 p-5 text-center d-flex flex-column align-items-center justify-content-center" style="min-height: 400px; background: rgba(255, 255, 255, 0.4); backdrop-filter: blur(16px); border: 1px dashed rgba(226, 232, 240, 0.9);">
    <!-- Animated Icon Box -->
    <div class="mb-4 position-relative">
        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background: color-mix(in srgb, var(--primary-accent) 10%, transparent); box-shadow: 0 0 0 10px color-mix(in srgb, var(--primary-accent) 5%, transparent);">
            <i class="bi {{ $icon ?? 'bi-tools' }}" style="font-size: 32px; color: var(--primary-accent);"></i>
        </div>
        <!-- Decorative Elements -->
        <i class="bi bi-gear-fill position-absolute text-muted opacity-25" style="font-size: 20px; top: -10px; right: -15px; animation: spin 4s linear infinite;"></i>
        <i class="bi bi-stars position-absolute text-warning opacity-50" style="font-size: 24px; bottom: -5px; left: -15px;"></i>
    </div>

    <h4 class="fw-bold mb-2" style="color: var(--text-heading); font-family: 'Outfit', sans-serif; letter-spacing: -0.5px;">{{ $title ?? 'Fitur Sedang Dikembangkan' }}</h4>
    <p class="text-muted mb-4" style="font-size: 13.5px; max-width: 450px;">
        {{ $description ?? 'Modul ini masih dalam tahap pengembangan oleh tim teknis kami. Kami sedang mempersiapkan fitur terbaik untuk kebutuhan operasional Anda.' }}
    </p>

    <div class="d-flex gap-2">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary rounded-pill px-4 py-2" style="font-size: 13px; font-weight: 500;">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>

    <style>
        @keyframes spin { 100% { transform: rotate(360deg); } }
        html.dark-mode .card {
            background: rgba(30, 35, 40, 0.4) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }
    </style>
</div>
