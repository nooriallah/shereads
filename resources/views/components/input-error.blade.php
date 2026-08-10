@props(['for'])

@error($for)
    <span {{ $attributes->merge(['class' => 'text-danger small d-block']) }}>{{ $message }}</span>
@enderror
