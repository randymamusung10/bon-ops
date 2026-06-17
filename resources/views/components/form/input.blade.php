@props(['disabled' => false, 'readonly' => false, 'error' => false, 'size' => 'md'])

@php
    $sizeClass = '';
    if ($size === 'sm') $sizeClass = 'form-control-sm';
    elseif ($size === 'lg') $sizeClass = 'form-control-lg';
@endphp

<input {{ $disabled ? 'disabled' : '' }} {{ $readonly ? 'readonly' : '' }} {!! $attributes->merge(['class' => 'form-control custom-form-control ' . $sizeClass . ' ' . ($error ? 'is-invalid' : '')]) !!}>
