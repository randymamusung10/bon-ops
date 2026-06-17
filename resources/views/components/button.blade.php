@props([
    'type' => 'button',
    'variant' => 'primary', /* primary, secondary, outline-primary, outline-secondary, danger, success, light, warning, ghost-primary, ghost-danger, ghost-success, ghost-warning, ghost-info */
    'size' => 'md', /* xs, sm, md, lg, xl */
    'icon' => null,
    'block' => false,
])

@php
    // Base classes
    $classes = 'btn custom-btn position-relative overflow-hidden ';
    
    // Sizing class
    $classes .= "btn-size-{$size} ";

    // Variant class
    $classes .= "btn-variant-{$variant} ";

    if ($block) $classes .= 'w-100 ';
@endphp

@once
<style>
    /* Sizes and Font Sizes */
    .btn-size-xs {
        padding: 5px 10px !important;
        font-size: 11.5px !important;
        border-radius: 6px !important;
    }
    .btn-size-sm {
        padding: 7px 18px !important;
        font-size: 13px !important;
        border-radius: 8px !important;
    }
    .btn-size-md {
        padding: 10px 22px !important;
        font-size: 14px !important;
        border-radius: 10px !important;
    }
    .btn-size-lg {
        padding: 12px 26px !important;
        font-size: 15px !important;
        border-radius: 12px !important;
    }
    .btn-size-xl {
        padding: 14px 30px !important;
        font-size: 17px !important;
        border-radius: 12px !important;
    }

    /* Variant Backgrounds & Colors */
    .btn-variant-primary {
        background-color: var(--primary-accent) !important;
        color: #ffffff !important;
        border: none !important;
    }
    .btn-variant-primary:hover {
        background-color: var(--primary-hover) !important;
        color: #ffffff !important;
    }
    
    .btn-variant-secondary {
        background-color: var(--secondary-accent) !important;
        color: #ffffff !important;
        border: none !important;
    }
    
    .btn-variant-light {
        background-color: #f1f5f9 !important;
        color: #475569 !important;
        border: none !important;
    }
    .btn-variant-light:hover {
        background-color: #e2e8f0 !important;
        color: #334155 !important;
    }
    
    .btn-variant-warning {
        background-color: #f97316 !important;
        color: #ffffff !important;
        border: none !important;
    }
    .btn-variant-warning:hover {
        background-color: #ea580c !important;
        color: #ffffff !important;
    }
    
    .btn-variant-danger {
        background-color: #ef4444 !important;
        color: #ffffff !important;
        border: none !important;
    }
    .btn-variant-danger:hover {
        background-color: #dc2626 !important;
        color: #ffffff !important;
    }
    
    .btn-variant-success {
        background-color: #10b981 !important;
        color: #ffffff !important;
        border: none !important;
    }
    .btn-variant-success:hover {
        background-color: #059669 !important;
        color: #ffffff !important;
    }
    
    /* Ghost / Soft variants */
    .btn-variant-ghost-success {
        background-color: rgba(16, 185, 129, 0.08) !important;
        color: #10b981 !important;
        border: none !important;
    }
    .btn-variant-ghost-success:hover {
        background-color: rgba(16, 185, 129, 0.16) !important;
        color: #059669 !important;
    }
    
    .btn-variant-ghost-danger {
        background-color: rgba(239, 68, 68, 0.08) !important;
        color: #ef4444 !important;
        border: none !important;
    }
    .btn-variant-ghost-danger:hover {
        background-color: rgba(239, 68, 68, 0.16) !important;
        color: #dc2626 !important;
    }

    .btn-variant-ghost-primary {
        background-color: color-mix(in srgb, var(--primary-accent) 8%, transparent) !important;
        color: var(--primary-accent) !important;
        border: none !important;
    }
    .btn-variant-ghost-primary:hover {
        background-color: color-mix(in srgb, var(--primary-accent) 16%, transparent) !important;
        color: var(--primary-hover) !important;
    }
</style>
@endonce

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    <span class="btn-content d-flex align-items-center justify-content-center gap-2" style="position: relative; z-index: 2;">
        @if($icon)
            <i class="bi {{ $icon }}"></i>
        @endif
        <span style="font-weight: 600;">{{ $slot }}</span>
    </span>
    <span class="ripple-overlay"></span>
</button>

