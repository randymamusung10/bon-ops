@unless ($breadcrumbs->isEmpty())
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb mb-0" style="font-size: 12px; font-weight: 500;">
            @foreach ($breadcrumbs as $breadcrumb)
                @if ($breadcrumb->url && !$loop->last)
                    <li class="breadcrumb-item">
                        <a href="{{ $breadcrumb->url }}" class="text-decoration-none text-muted">
                            @if ($loop->first)
                                <i class="bi bi-house-door me-1"></i>
                            @endif
                            {{ $breadcrumb->title }}
                        </a>
                    </li>
                @else
                    <li class="breadcrumb-item active" aria-current="page" style="color: var(--primary-accent);">
                        @if ($loop->first)
                            <i class="bi bi-house-door me-1"></i>
                        @endif
                        {{ $breadcrumb->title }}
                    </li>
                @endif
            @endforeach
        </ol>
    </nav>
@endunless
