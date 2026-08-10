<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-danger px-4']) }}>
    {{ $slot }}
</button>
