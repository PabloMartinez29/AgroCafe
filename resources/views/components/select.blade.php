@props(['label' => null, 'name', 'required' => false, 'error' => null, 'id' => null])

@php
    $selectId = $id ?? $name;
@endphp

<div class="mb-4">
    @if($label)
        <label for="{{ $selectId }}" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <select 
        id="{{ $selectId }}"
        name="{{ $name }}"
        {{ $attributes->merge(['class' => 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-coffee-500 focus:border-coffee-500 transition-colors']) }}
        @if($required) required @endif
    >
        {{ $slot }}
    </select>
    
    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>

