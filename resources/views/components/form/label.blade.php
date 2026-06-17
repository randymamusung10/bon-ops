@props(['value', 'required' => false])

<label {{ $attributes->merge(['class' => 'form-label custom-form-label']) }}>
    {{ $value ?? $slot }}
    @if ($required)
        <span class="text-danger ms-1">*</span>
    @endif
</label>
