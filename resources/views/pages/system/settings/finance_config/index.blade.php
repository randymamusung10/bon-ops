@extends('layouts.app')

@section('page_title', 'Konfigurasi Keuangan')
@section('page_description', 'Pilih pemetaan Bagan Akun (Chart of Accounts) standar untuk penjurnalan otomatis transaksi POS dan Logistik.')

@section('content')
<div class="container-fluid px-0">
    <div class="row">
        <div class="col-lg-8 col-xl-6">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card rounded-4 border-0 shadow-sm p-4" style="background: var(--bg-dark-secondary);">
                <form action="{{ route('system.settings.finance_config.update') }}" method="POST">
                    @csrf
                    
                    <h5 class="mb-4 text-heading fw-semibold"><i class="bi bi-bank me-2 text-primary"></i>Pemetaan Akun Default</h5>
                    
                    <div class="mb-3">
                        <x-form.label>Akun Kas / Pembayaran Default</x-form.label>
                        <x-form.select name="cash_account_id" class="select2">
                            <option value="">-- Pilih Akun --</option>
                            @foreach($coas as $coa)
                                <option value="{{ $coa->id }}" {{ ($config->cash_account_id ?? '') == $coa->id ? 'selected' : '' }}>
                                    {{ $coa->code }} - {{ $coa->name }}
                                </option>
                            @endforeach
                        </x-form.select>
                        <div class="form-text mt-1 text-muted" style="font-size: 12px;">Digunakan sebagai akun penerimaan kas dari transaksi POS.</div>
                        @error('cash_account_id') <div class="text-danger mt-1" style="font-size: 12px;">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <x-form.label>Akun Pendapatan Penjualan (Sales Revenue)</x-form.label>
                        <x-form.select name="sales_revenue_account_id" class="select2">
                            <option value="">-- Pilih Akun --</option>
                            @foreach($coas as $coa)
                                <option value="{{ $coa->id }}" {{ ($config->sales_revenue_account_id ?? '') == $coa->id ? 'selected' : '' }}>
                                    {{ $coa->code }} - {{ $coa->name }}
                                </option>
                            @endforeach
                        </x-form.select>
                        <div class="form-text mt-1 text-muted" style="font-size: 12px;">Digunakan untuk mencatat nilai pendapatan bersih transaksi POS.</div>
                        @error('sales_revenue_account_id') <div class="text-danger mt-1" style="font-size: 12px;">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <x-form.label>Akun Hutang Pajak / PPN Keluaran</x-form.label>
                        <x-form.select name="tax_payable_account_id" class="select2">
                            <option value="">-- Pilih Akun --</option>
                            @foreach($coas as $coa)
                                <option value="{{ $coa->id }}" {{ ($config->tax_payable_account_id ?? '') == $coa->id ? 'selected' : '' }}>
                                    {{ $coa->code }} - {{ $coa->name }}
                                </option>
                            @endforeach
                        </x-form.select>
                        <div class="form-text mt-1 text-muted" style="font-size: 12px;">Digunakan untuk mencatat nilai pajak yang dipungut dari pelanggan.</div>
                        @error('tax_payable_account_id') <div class="text-danger mt-1" style="font-size: 12px;">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <x-form.label>Akun Harga Pokok Penjualan (COGS)</x-form.label>
                        <x-form.select name="cogs_account_id" class="select2">
                            <option value="">-- Pilih Akun --</option>
                            @foreach($coas as $coa)
                                <option value="{{ $coa->id }}" {{ ($config->cogs_account_id ?? '') == $coa->id ? 'selected' : '' }}>
                                    {{ $coa->code }} - {{ $coa->name }}
                                </option>
                            @endforeach
                        </x-form.select>
                        <div class="form-text mt-1 text-muted" style="font-size: 12px;">Digunakan jika produk memiliki modal (cost price).</div>
                        @error('cogs_account_id') <div class="text-danger mt-1" style="font-size: 12px;">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <x-form.label>Akun Persediaan (Inventory)</x-form.label>
                        <x-form.select name="inventory_account_id" class="select2">
                            <option value="">-- Pilih Akun --</option>
                            @foreach($coas as $coa)
                                <option value="{{ $coa->id }}" {{ ($config->inventory_account_id ?? '') == $coa->id ? 'selected' : '' }}>
                                    {{ $coa->code }} - {{ $coa->name }}
                                </option>
                            @endforeach
                        </x-form.select>
                        <div class="form-text mt-1 text-muted" style="font-size: 12px;">Digunakan untuk mengurangi nilai persediaan barang.</div>
                        @error('inventory_account_id') <div class="text-danger mt-1" style="font-size: 12px;">{{ $message }}</div> @enderror
                    </div>

                    <hr class="border-secondary mb-4 opacity-25">

                    <div class="d-flex justify-content-end">
                        <x-button type="submit" variant="primary" size="sm" icon="bi-check2">Simpan Konfigurasi</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- Pilih Akun --'
        });
    });
</script>
@endpush
