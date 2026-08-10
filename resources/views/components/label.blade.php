@props(['value'])

<label {{ $attributes->merge(['class' => 'form-label font-w500']) }}>
    {{ $value ?? $slot }}
</label>
