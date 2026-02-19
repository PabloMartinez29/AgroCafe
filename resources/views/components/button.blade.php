@props(['variant' => 'primary', 'size' => 'md', 'type' => 'button'])

@php
    $variants = [
        'primary' => 'bg-coffee-600 hover:bg-coffee-700 text-white',
        'secondary' => 'bg-gray-200 hover:bg-gray-300 text-gray-800',
        'success' => 'bg-green-600 hover:bg-green-700 text-white',
        'danger' => 'bg-red-600 hover:bg-red-700 text-white',
        'warning' => 'bg-yellow-500 hover:bg-yellow-600 text-white',
        'info' => 'bg-blue-600 hover:bg-blue-700 text-white',
    ];

    $sizes = [
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-base',
        'lg' => 'px-6 py-3 text-lg',
    ];

    $classes = $variants[$variant] . ' ' . $sizes[$size] . ' rounded-lg font-medium transition-colors duration-200 inline-flex items-center justify-center space-x-2';
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>

