@props(['disabled' => false, 'error' => false])

<select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'form-select custom-form-control ' . ($error ? 'is-invalid' : '')]) !!}>
    {{ $slot }}
</select>
