@props(['assisted' => false])

@php
    $items = [
        'accept_terms' => 'terms',
        'accept_privacy' => 'privacy',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'space-y-3']) }} data-consent-checkboxes>
    @foreach ($items as $field => $document)
        @php
            $config = config("legal.documents.{$document}");
            $text = $assisted ? $config['checkbox_assisted'] : $config['checkbox'];
        @endphp

        <div>
            <div class="flex items-start gap-3 rounded-xl border-2 bg-gray-50 p-4 {{ $errors->has($field) ? 'border-red-300' : 'border-gray-200' }}">
                <input
                    type="checkbox"
                    id="{{ $field }}"
                    name="{{ $field }}"
                    value="1"
                    required
                    @checked(old($field))
                    class="mt-1 h-5 w-5 shrink-0 rounded border-gray-300 text-coffee-600 focus:ring-coffee-500"
                >
                <label for="{{ $field }}" class="text-sm leading-relaxed text-gray-700">
                    {{ $text }}
                    <a
                        href="{{ route($config['route']) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="font-semibold text-coffee-600 underline hover:text-coffee-700"
                    >{{ $config['link_label'] }}</a>
                </label>
            </div>
            @error($field)
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    @endforeach
</div>
