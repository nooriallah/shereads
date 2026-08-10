<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn text-white px-4', 'style' => 'background:#05653D;']) }}>
    {{ $slot }}
</button>
