@props(['id' => null, 'maxWidth' => null])

<x-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <div class="p-4 border-bottom">
        <h4 class="fs-18 font-w600 mb-0">
            {{ $title }}
        </h4>
    </div>

    <div class="p-4">
        {{ $content }}
    </div>

    <div class="d-flex justify-content-end gap-2 p-3" style="background: var(--sr-gray-100, #EDEDED);">
        {{ $footer }}
    </div>
</x-modal>
