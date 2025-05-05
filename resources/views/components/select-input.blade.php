@props(['name', 'label', 'options' => [], 'selected' => null, 'required' => false])

<div class="mb-4">
    <label for="{{ $name }}" class="block font-medium text-sm text-gray-700">{{ $label }}</label>
    <select name="{{ $name }}" id="{{ $name }}" @if($required) required @endif
            class="form-select mt-1 block w-full rounded-md shadow-sm border-gray-300">
        <option value="">-- Selecciona --</option>
        @foreach ($options as $key => $value)
            <option value="{{ $key }}" {{ (string)$key === (string)$selected ? 'selected' : '' }}>
                {{ $value }}
            </option>
        @endforeach
    </select>
</div>
