@props(['text' => '', 'href' => null, 'onclick' => null, 'type' => 'button'])

@if ($href)
    <a href="{{ $href }}"
       {{ $attributes->merge([
            'class' => 'inline-block px-6 py-2 bg-gray-200 text-gray-800 font-semibold rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 transition duration-200 text-center mb-4 md:mb-0'
       ]) }}>
        {{ $text }}
    </a>
@else
    <button type="{{ $type }}"
            @if ($onclick) onclick="{{ $onclick }}" @endif
            {{ $attributes->merge([
                'class' => 'inline-block px-6 py-2 bg-gray-200 text-gray-800 font-semibold rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 transition duration-200 text-center mb-4 md:mb-0'
            ]) }}>
        {{ $text }}
    </button>
@endif
