@props(['id' => 'defaultModal', 'title' => null, 'size' => 'md'])

@php
    $sizes = [
        'sm' => 'max-w-md',
        'md' => 'max-w-2xl',
        'lg' => 'max-w-4xl',
        'xl' => 'max-w-6xl',
    ];
@endphp

<!-- Modal Overlay -->
<div id="{{ $id }}" 
     class="fixed inset-0 z-50 overflow-y-auto"
     x-data="{ show: false }"
     x-show="show"
     x-cloak
     style="display: none;"
     @open-modal.window="$event.detail.id === '{{ $id }}' && (show = true, document.body.style.overflow = 'hidden')"
     @close-modal.window="$event.detail.id === '{{ $id }}' && (show = false, document.body.style.overflow = '')"
     x-on:keydown.escape.window="show = false; document.body.style.overflow = ''">
    
    <!-- Backdrop con blur -->
    <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-md"
         x-show="show"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="show = false; document.body.style.overflow = ''"></div>

    <!-- Modal Container -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full {{ $sizes[$size] }} transform overflow-hidden rounded-xl bg-white shadow-2xl"
             x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.stop>
            
            <!-- Header -->
            @if($title)
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
                    <button @click="show = false; document.body.style.overflow = ''" 
                            class="text-gray-400 hover:text-gray-600 transition-colors rounded-full p-1 hover:bg-gray-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            @endif

            <!-- Content -->
            <div class="px-6 py-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
