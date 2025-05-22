@props(['text' => '', 'href' => null, 'onclick' => null, 'type' => 'button'])

@if ($href)
    <a href="{{ $href }}"
       {{ $attributes->merge([
            'class' => 'inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-center mb-4 md:mb-0',
       ]) }}>
        {{ $text }}
    </a>
@else
    <button type="{{ $type }}"
            onclick="{{ $onclick }}"
            {{ $attributes->merge([
                'class' => 'inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-center mb-4 md:mb-0',
            ]) }}>
        {{ $text }}
    </button>
@endif
