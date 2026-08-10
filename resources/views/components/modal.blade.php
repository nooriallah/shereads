@props(['id', 'maxWidth'])

@php
$id = $id ?? md5($attributes->wire('model'));

$maxWidth = [
    'sm' => '400px',
    'md' => '480px',
    'lg' => '560px',
    'xl' => '640px',
    '2xl' => '720px',
][$maxWidth ?? '2xl'];
@endphp

<div
    x-data="{ show: @entangle($attributes->wire('model')) }"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    id="{{ $id }}"
    class="jetstream-modal"
    style="display: none; position: fixed; inset: 0; z-index: 1055; overflow-y: auto; padding: 1.5rem 1rem;"
>
    {{-- Overlay --}}
    <div x-show="show" x-on:click="show = false"
        x-transition:enter="transition-opacity" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        style="position: fixed; inset: 0; background: rgba(33, 37, 41, .6);"></div>

    {{-- Dialog --}}
    <div x-show="show" x-trap.inert.noscroll="show"
        x-transition:enter="transition-opacity" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="bg-white rounded-3 shadow-lg overflow-hidden"
        style="position: relative; margin: 3rem auto; width: 100%; max-width: {{ $maxWidth }};">
        {{ $slot }}
    </div>
</div>
