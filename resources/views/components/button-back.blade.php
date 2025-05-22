@props(['href' => '#', 'text' => 'Volver'])

<a href="{{ $href }}"
   class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 text-sm font-semibold rounded-md shadow-sm hover:bg-gray-300 transition"
>
    <svg xmlns="http://www.w3.org/2000/svg"
         fill="none"
         viewBox="0 0 24 24"
         stroke-width="1.5"
         stroke="currentColor"
         class="w-5 h-5 mr-2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
    </svg>
    {{ $text }}
</a>
