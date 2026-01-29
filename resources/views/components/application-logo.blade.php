@php
        $sizePx = isset($size) ? (is_numeric($size) ? $size.'px' : $size) : '64px';
@endphp

<img
        src="{{ asset('images/logo.png') }}"
        alt="Taskem Management"
        {{ $attributes->merge(['class' => 'block rounded-lg shadow-sm', 'style' => "width: {$sizePx}; height: {$sizePx}; object-fit: contain;"] ) }}
/>
