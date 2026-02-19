@props(['title' => null, 'icon' => null])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-100']) }}>
    @if($title || $icon)
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                @if($icon)
                    <i class="{{ $icon }} mr-2"></i>
                @endif
                {{ $title }}
            </h3>
        </div>
    @endif
    <div class="p-6">
        {{ $slot }}
    </div>
</div>

