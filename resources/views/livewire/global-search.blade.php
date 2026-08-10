<div class="position-relative w-100" x-data="{ open: false }" @click.outside="open = false"
    @keydown.escape.window="open = false">

    <form class="input-group search-area w-100" style="height: 47px;" onsubmit="return false;" role="search">
        <span class="input-group-text h-100">
            <a href="javascript:void(0)">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M11 0.25C5.61522 0.25 1.25 4.61522 1.25 10C1.25 15.3848 5.61522 19.75 11 19.75C16.3848 19.75 20.75 15.3848 20.75 10C20.75 4.61522 16.3848 0.25 11 0.25ZM2.75 10C2.75 5.44365 6.44365 1.75 11 1.75C15.5563 1.75 19.25 5.44365 19.25 10C19.25 14.5563 15.5563 18.25 11 18.25C6.44365 18.25 2.75 14.5563 2.75 10Z"
                        fill="#C8C8C8" />
                    <path
                        d="M19.5304 17.4698C19.2375 17.1769 18.7626 17.1769 18.4697 17.4698C18.1768 17.7626 18.1768 18.2375 18.4697 18.5304L22.4696 22.5304C22.7625 22.8233 23.2374 22.8233 23.5303 22.5304C23.8232 22.2375 23.8232 21.7626 23.5303 21.4697L19.5304 17.4698Z"
                        fill="#C8C8C8" />
                </svg>
            </a>
        </span>
        <input type="text" class="form-control h-100" autocomplete="off"
            placeholder="Search for books, authors, categories{{ $isAdmin ? ', users' : '' }}..."
            wire:model.live.debounce.350ms="query" @focus="open = true" @input="open = true">
    </form>

    @if ($ready)
        <div class="position-absolute start-0 end-0 bg-white rounded shadow-lg border overflow-auto"
            style="z-index: 1050; max-height: 420px; top: calc(100% + 4px);" x-show="open"
            wire:key="global-search-results">

            @if (! $hasResults)
                <div class="p-3 text-muted">No results for "{{ trim($query) }}"</div>
            @else

                {{-- Books --}}
                @if ($results->get('books', collect())->isNotEmpty())
                    <div class="px-3 pt-3 pb-1 fs-12 text-uppercase font-w600 text-accent">Books</div>
                    @foreach ($results['books'] as $book)
                        @if ($isAdmin)
                            <a href="{{ route('books', ['search' => $book->title]) }}"
                                class="search-result-item dropdown-item d-flex justify-content-between align-items-center py-2">
                                <span>
                                    <span class="d-block font-w600">{{ $book->title }}</span>
                                    <small class="text-muted">{{ $book->authors->pluck('full_name')->join(', ') ?: 'No author' }}</small>
                                </span>
                                <span @class([
                                    'badge',
                                    'badge-success' => $book->status === \App\Models\Book::STATUS_PUBLISHED,
                                    'badge-warning' => $book->status === \App\Models\Book::STATUS_DRAFT,
                                    'badge-secondary' => $book->status === \App\Models\Book::STATUS_ARCHIVED,
                                ])>{{ ucfirst($book->status) }}</span>
                            </a>
                        @else
                            <div class="search-result-item dropdown-item py-2">
                                <span class="d-block font-w600">{{ $book->title }}</span>
                                <small class="text-muted">{{ $book->authors->pluck('full_name')->join(', ') ?: '' }}</small>
                            </div>
                        @endif
                    @endforeach
                @endif

                {{-- Authors --}}
                @if ($results->get('authors', collect())->isNotEmpty())
                    <div class="px-3 pt-3 pb-1 fs-12 text-uppercase font-w600 text-accent">Authors</div>
                    @foreach ($results['authors'] as $author)
                        @if ($isAdmin)
                            <a href="{{ route('authors') }}" class="search-result-item dropdown-item py-2">
                                <span class="d-block font-w600">{{ $author->full_name }}</span>
                                @if ($author->country)
                                    <small class="text-muted">{{ $author->country }}</small>
                                @endif
                            </a>
                        @else
                            <div class="search-result-item dropdown-item py-2">
                                <span class="d-block font-w600">{{ $author->full_name }}</span>
                            </div>
                        @endif
                    @endforeach
                @endif

                {{-- Categories --}}
                @if ($results->get('categories', collect())->isNotEmpty())
                    <div class="px-3 pt-3 pb-1 fs-12 text-uppercase font-w600 text-accent">Categories</div>
                    @foreach ($results['categories'] as $category)
                        @if ($isAdmin)
                            <a href="{{ route('categories') }}" class="search-result-item dropdown-item py-2 font-w600">
                                {{ $category->name }}
                            </a>
                        @else
                            <div class="search-result-item dropdown-item py-2 font-w600">{{ $category->name }}</div>
                        @endif
                    @endforeach
                @endif

                @if ($isAdmin)
                    {{-- Interests --}}
                    @if ($results->get('interests', collect())->isNotEmpty())
                        <div class="px-3 pt-3 pb-1 fs-12 text-uppercase font-w600 text-accent">Interests</div>
                        @foreach ($results['interests'] as $interest)
                            <a href="{{ route('interests') }}" class="search-result-item dropdown-item py-2 font-w600">
                                {{ $interest->name }}
                            </a>
                        @endforeach
                    @endif

                    {{-- Users --}}
                    @if ($results->get('users', collect())->isNotEmpty())
                        <div class="px-3 pt-3 pb-1 fs-12 text-uppercase font-w600 text-accent">Users</div>
                        @foreach ($results['users'] as $user)
                            <a href="{{ route('users', ['search' => $user->email]) }}"
                                class="search-result-item dropdown-item d-flex justify-content-between align-items-center py-2">
                                <span>
                                    <span class="d-block font-w600">{{ $user->full_name }}</span>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </span>
                                <span class="badge light badge-primary">
                                    {{ \App\Enums\UserRole::tryFrom($user->role)?->label() ?? ucfirst($user->role) }}
                                </span>
                            </a>
                        @endforeach
                    @endif
                @endif

            @endif
        </div>
    @endif
</div>
