@props(['name', 'label', 'checked' => false])

<div class="mb-4 flex items-center">
    <input type="checkbox" name="{{ $name }}" value="0" id="{{ $name }}" {{ $checked ? 'checked' : '' }}
           class="form-checkbox rounded text-purple-600 shadow-sm border-gray-300">
    <label for="{{ $name }}" class="ml-2 text-sm text-gray-700">{{ $label }}</label>
</div>
