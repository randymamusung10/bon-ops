@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <div class="row align-items-center mb-4">
        <div class="col-12">
            <!-- Breadcrumb Navigation -->
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb mb-0" style="font-size: 12px; font-weight: 500;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted"><i class="bi bi-house-door me-1"></i>Home</a></li>
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Sistem</a></li>
                    <li class="breadcrumb-item active" aria-current="page" style="color: var(--primary-accent);">Manajemen User</li>
                </ol>
            </nav>
            <h1 class="h4 fw-bold mb-1" style="color: var(--text-heading); font-family: 'Outfit', sans-serif; letter-spacing: -0.5px;">Manajemen User</h1>
            <p class="mb-0" style="color: var(--text-light); font-size: 13.5px;">Pengaturan hak akses dan profil pengguna BonOps.</p>
        </div>
    </div>

    <x-under-construction 
        icon="bi-people-fill" 
        title="Modul Manajemen User Sedang Disiapkan" 
        description="Fitur pengelolaan pengguna, role matrix, dan permission policy sedang dikembangkan. Segera hadir pada fase deployment berikutnya." 
    />
</div>
@endsection