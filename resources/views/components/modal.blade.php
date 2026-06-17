@props([
    'id',
    'title',
    'size' => 'md', /* sm, md, lg, xl, fullscreen */
    'description' => '',
])

@php
    $dialogClasses = 'modal-dialog ';
    if ($size === 'fullscreen') {
        $dialogClasses .= 'modal-fullscreen ';
    } elseif ($size && $size !== 'md') {
        $dialogClasses .= "modal-{$size} ";
    }
@endphp

<div class="modal fade custom-modal" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true" {{ $attributes }}>
    <div class="{{ $dialogClasses }}" style="margin-top: 3.5rem;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: {{ $size === 'fullscreen' ? '0' : '20px' }}; background: var(--bg-dark-secondary);">
            
            <div class="modal-header align-items-center" style="border-bottom: 1px solid var(--border-color); padding: 20px 28px;">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="{{ $id }}Label" style="color: var(--text-heading); font-family: 'Outfit', sans-serif; font-size: 17px;">
                        {{ $title }}
                    </h5>
                    @if($description)
                        <p class="mb-0 text-muted mt-1" style="font-size: 12.5px;">{{ $description }}</p>
                    @endif
                </div>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body" style="padding: 24px 28px;">
                {{ $slot }}
            </div>

            @if(isset($footer))
                <div class="modal-footer" style="border-top: 1px solid var(--border-color); padding: 16px 28px; background: color-mix(in srgb, var(--bg-dark-secondary) 95%, black);">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
