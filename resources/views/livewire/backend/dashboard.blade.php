{{-- Admin dashboard section --}}
@if ($isAdmin)
<div class="container-fluid">

    {{-- Core counts --}}
    <div class="row">
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body d-flex px-4 justify-content-between align-items-center">
                    <div>
                        <h2 class="fs-32 font-w700 mb-0">{{ number_format($totalBooks) }}</h2>
                        <span class="fs-18 font-w500 d-block">Total Books</span>
                        <small class="d-block fs-14 text-muted">
                            {{ $publishedBooks }} published · {{ $draftBooks }} draft
                        </small>
                    </div>
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width:56px;height:56px;background:rgba(5,101,61,.1);">
                        <i class="fa fa-book fs-24" style="color:#05653D;"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body d-flex px-4 justify-content-between align-items-center">
                    <div>
                        <h2 class="fs-32 font-w700 mb-0">{{ number_format($totalAuthors) }}</h2>
                        <span class="fs-18 font-w500 d-block">Authors</span>
                        <small class="d-block fs-14 text-muted">&nbsp;</small>
                    </div>
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width:56px;height:56px;background:rgba(231,185,68,.15);">
                        <i class="fa fa-pen-nib fs-24" style="color:#E7B944;"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body d-flex px-4 justify-content-between align-items-center">
                    <div>
                        <h2 class="fs-32 font-w700 mb-0">{{ number_format($totalCategories) }}</h2>
                        <span class="fs-18 font-w500 d-block">Categories</span>
                        <small class="d-block fs-14 text-muted">&nbsp;</small>
                    </div>
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width:56px;height:56px;background:rgba(5,101,61,.1);">
                        <i class="fa fa-layer-group fs-24" style="color:#05653D;"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body d-flex px-4 justify-content-between align-items-center">
                    <div>
                        <h2 class="fs-32 font-w700 mb-0">{{ number_format($totalInterests) }}</h2>
                        <span class="fs-18 font-w500 d-block">Interests</span>
                        <small class="d-block fs-14 text-muted">&nbsp;</small>
                    </div>
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width:56px;height:56px;background:rgba(231,185,68,.15);">
                        <i class="fa fa-heart fs-24" style="color:#E7B944;"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Users / publishing / questionnaire --}}
    <div class="row">
        <div class="col-xl-4 col-sm-6">
            <div class="card">
                <div class="card-body px-4">
                    <h4 class="fs-18 font-w600 mb-3">Users</h4>
                    <div class="d-flex align-items-end justify-content-between">
                        <h2 class="fs-32 font-w700 mb-0">{{ number_format($totalUsers) }}</h2>
                        <span class="text-muted">{{ $totalReaders }} readers · {{ $totalStaff }} staff</span>
                    </div>
                    <hr class="my-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">New this week</span>
                        <span class="font-w600">{{ $newUsersThisWeek }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">New this month</span>
                        <span class="font-w600">{{ $newUsersThisMonth }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="card">
                <div class="card-body px-4">
                    <h4 class="fs-18 font-w600 mb-3">Publishing</h4>
                    @php
                    $publishedPct = $totalBooks > 0 ? round($publishedBooks / $totalBooks * 100) : 0;
                    @endphp
                    <div class="d-flex align-items-end justify-content-between mb-2">
                        <h2 class="fs-32 font-w700 mb-0">{{ $publishedPct }}%</h2>
                        <span class="text-muted">of books published</span>
                    </div>
                    <div class="progress default-progress" style="height:10px;">
                        <div class="progress-bar progress-animated" role="progressbar" style="width: {{ $publishedPct }}%; height:10px; background:#05653D;" aria-valuenow="{{ $publishedPct }}" aria-valuemin="0" aria-valuemax="100">
                            <span class="sr-only">{{ $publishedPct }}% published</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-3">
                        <span class="text-muted">Draft</span>
                        <span class="font-w600">{{ $draftBooks }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Archived</span>
                        <span class="font-w600">{{ $archivedBooks }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-12">
            <div class="card">
                <div class="card-body px-4">
                    <h4 class="fs-18 font-w600 mb-3">Questionnaire</h4>
                    @php
                    $completedPct = $totalResponses > 0 ? round($completedResponses / $totalResponses * 100) : 0;
                    @endphp
                    <div class="d-flex align-items-end justify-content-between mb-2">
                        <h2 class="fs-32 font-w700 mb-0">{{ number_format($completedResponses) }}</h2>
                        <span class="text-muted">completed responses</span>
                    </div>
                    <div class="progress default-progress" style="height:10px;">
                        <div class="progress-bar progress-animated" role="progressbar" style="width: {{ $completedPct }}%; height:10px; background:#E7B944;" aria-valuenow="{{ $completedPct }}" aria-valuemin="0" aria-valuemax="100">
                            <span class="sr-only">{{ $completedPct }}% completed</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-3">
                        <span class="text-muted">Started in total</span>
                        <span class="font-w600">{{ number_format($totalResponses) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Completion rate</span>
                        <span class="font-w600">{{ $completedPct }}%</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Signed up after answering</span>
                        <span class="font-w600">{{ number_format($linkedResponses) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Signup trend --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body px-4">
                    <h4 class="fs-18 font-w600 mb-4">New users — last 8 weeks</h4>
                    @php
                    $maxSignups = max(1, collect($signupsPerWeek)->max('count'));
                    @endphp
                    <div class="d-flex align-items-end justify-content-between gap-2" style="height:160px;">
                        @foreach ($signupsPerWeek as $week)
                        <div class="d-flex flex-column align-items-center justify-content-end flex-fill h-100">
                            <span class="fs-14 font-w600 mb-1">{{ $week['count'] }}</span>
                            <div class="w-100 rounded-top" style="max-width:48px; background:#05653D; opacity:.85; height: {{ max(3, round($week['count'] / $maxSignups * 100)) }}%;" title="Week of {{ $week['label'] }}: {{ $week['count'] }} signups"></div>
                            <small class="text-muted mt-2 text-nowrap">{{ $week['label'] }}</small>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent activity --}}
    <div class="row">
        <div class="col-xl-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Latest Books</h4>
                    <a href="{{ route('books') }}" wire:navigate class="btn btn-sm" style="background:#05653D;color:#fff;">Manage books</a>
                </div>
                <div class="card-body">
                    @if ($latestBooks->isEmpty())
                    <p class="text-muted mb-0">No books yet. Add your first book to see it here.</p>
                    @else
                    <div class="table-responsive">
                        <table class="table verticle-middle table-responsive-sm">
                            <thead>
                                <tr>
                                    <th scope="col">Title</th>
                                    <th scope="col">Author(s)</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Added</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($latestBooks as $book)
                                <tr>
                                    <td class="font-w600">{{ $book->title }}</td>
                                    <td>{{ $book->authors->pluck('full_name')->join(', ') ?: '—' }}</td>
                                    <td>
                                        <span @class([ 'badge' , 'badge-success'=> $book->status === \App\Models\Book::STATUS_PUBLISHED,
                                            'badge-warning' => $book->status === \App\Models\Book::STATUS_DRAFT,
                                            'badge-secondary' => $book->status === \App\Models\Book::STATUS_ARCHIVED,
                                            ])>{{ ucfirst($book->status) }}</span>
                                    </td>
                                    <td>{{ $book->created_at->format('d M Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Newest Users</h4>
                    <a href="{{ route('users') }}" wire:navigate class="btn btn-sm" style="background:#05653D;color:#fff;">Manage users</a>
                </div>
                <div class="card-body">
                    @if ($newestUsers->isEmpty())
                    <p class="text-muted mb-0">No users yet.</p>
                    @else
                    <div class="table-responsive">
                        <table class="table verticle-middle table-responsive-sm">
                            <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Role</th>
                                    <th scope="col">Questionnaire</th>
                                    <th scope="col">Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($newestUsers as $user)
                                <tr>
                                    <td>
                                        <span class="font-w600 d-block">{{ $user->full_name }}</span>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </td>
                                    <td>{{ \App\Enums\UserRole::tryFrom($user->role)?->label() ?? ucfirst($user->role) }}</td>
                                    <td>
                                        @if ($user->completed_questionnaire)
                                        <span class="badge badge-success">Completed</span>
                                        @else
                                        <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('d M Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@else
<!-- Reader dashboard -->
<div class="container-fluid">

    <div class="bg-white m-0 pt-0 pb-md-3">
        <h1 class="title-welcome">Welcome {{ auth()->user()->full_name }}!</h1>
    </div>

    {{-- Filters --}}
    <div class="filter_wrapper bg-white m-0 pt-0 pb-md-3 d-md-flex flex-wrap gap-2 justify-content-between">
        <button type="button" wire:click="setFilter('all')" @class(['btn-filter btn', 'btn-success'=> $filter === 'all', 'btn-outline-success' => $filter !== 'all'])>
            All
        </button>
        <button type="button" wire:click="setFilter('recommended')" @class(['btn-filter btn', 'btn-success'=> $filter === 'recommended', 'btn-outline-success' => $filter !== 'recommended'])>
            Recommended
        </button>
        <button type="button" wire:click="setFilter('new')" @class(['btn-filter btn', 'btn-success'=> $filter === 'new', 'btn-outline-success' => $filter !== 'new'])>
            New Arrivals
        </button>
        <button type="button" wire:click="setFilter('top')" @class(['btn-filter btn', 'btn-success'=> $filter === 'top', 'btn-outline-success' => $filter !== 'top'])>
            Top-Rated
        </button>

        <select class="form-select" style="max-width: 172px;" wire:model.live="categoryId">
            <option value="">Genre</option>
            @foreach ($allCategories as $cat)
            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>

        <select class="form-select" style="max-width: 172px;" wire:model.live="authorId">
            <option value="">Author</option>
            @foreach ($allAuthors as $a)
            <option value="{{ $a->id }}">{{ trim($a->name . ' ' . $a->lastname) }}</option>
            @endforeach
        </select>
    </div>

    {{-- Continue Reading strip (home shelf only) --}}
    @if ($continueReading->isNotEmpty())
    <div class="mt-3">
        <h4 class="fs-18 font-w600 mb-3">Continue Reading</h4>
        <div class="row g-3">
            @foreach ($continueReading as $progress)
            <div class="col-12 col-md-6 col-xl-3" wire:key="cr-{{ $progress->id }}">
                <a href="{{ route('read', $progress->book_id) }}" wire:navigate class="card mb-0 text-decoration-none h-100">
                    <div class="card-body d-flex gap-3 align-items-center py-3">
                        <img src="{{ $progress->book->cover_image ? asset('storage/' . $progress->book->cover_image) : asset('backend/images/books/bookpic.png') }}" alt="{{ $progress->book->title }}" width="48" height="64" class="rounded" style="object-fit: cover;">
                        <div class="flex-fill">
                            <span class="d-block font-w600">{{ $progress->book->title }}</span>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ $progress->progress_percent }}%; background:#05653D;"></div>
                            </div>
                            <small class="text-muted">{{ $progress->progress_percent }}% · page {{ $progress->current_position ?: 1 }}</small>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        <hr class="my-4">
    </div>
    @endif

    {{-- Shelf title for personal lists --}}
    @if (in_array($filter, ['favorites', 'saved', 'reading']))
    <h4 class="fs-18 font-w600 mt-3">
        {{ ['favorites' => 'My Favourite Books', 'saved' => 'My Saved Books', 'reading' => 'My Reading Room'][$filter] }}
    </h4>
    @endif

    {{-- Reading Room: in-progress books with position --}}
    @if ($filter === 'reading')
    <div class="row g-4 mt-1">
        @forelse ($inProgress as $progress)
        <div class="col-6 col-sm-4 col-md-3 col-xl-2" wire:key="rp-{{ $progress->id }}">
            <div class="book_wrapper">
                <img src="{{ $progress->book->cover_image ? asset('storage/' . $progress->book->cover_image) : asset('backend/images/books/bookpic.png') }}" class="w-100" alt="{{ $progress->book->title }}" style="aspect-ratio: 3/4; object-fit: cover;">
                {{-- Progress bar --}}
                <div class="progress mt-1" style="height: 6px;">
                    <div class="progress-bar" role="progressbar" style="width: {{ $progress->progress_percent }}%; background:#05653D;"></div>
                </div>
                <small class="text-muted">
                    {{ $progress->completed_at ? 'Completed' : $progress->progress_percent . '% read' }}
                </small>

                <h6 class="mt-3 fw-bold mb-1">
                    {{ $progress->book->title }}
                    <br />
                    <span>{{ $progress->book->authors->pluck('full_name')->join(', ') }}</span>
                </h6>

                <div class="details d-flex justify-content-between mt-1">
                    <a href="{{ route('read', $progress->book_id) }}" wire:navigate class="btn_read btn">
                        {{ $progress->completed_at ? 'Read Again' : 'Continue' }}
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <p class="text-muted mt-3">You haven't started reading yet — open any book with "Read Now" and it will appear here.</p>
        </div>
        @endforelse
    </div>


    @else
    {{-- Book shelf --}}
    <div class="row g-4 mt-1">
        @forelse ($books as $book)
        <div class="col-6 col-sm-4 col-md-3 col-xl-2" wire:key="book-{{ $book->id }}">
            <div class="book_wrapper">
                <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('backend/images/books/bookpic.png') }}" class="w-100" alt="{{ $book->title }}" style="aspect-ratio: 3/4; object-fit: cover;">
                <h6 class="mt-3 fw-bold">
                    {{ $book->title }}
                    <br />
                    <span>{{ $book->authors->pluck('full_name')->join(', ') }}</span>
                </h6>

                <div class="details d-flex justify-content-between align-items-center">
                    <a href="{{ route('read', $book->id) }}" wire:navigate class="btn_read btn">Read Now</a>

                    {{-- save and fav icons  --}}
                    <span class="d-flex gap-1">
                        <button type="button" wire:click="toggleSaved({{ $book->id }})" class="btn btn-sm p-1 border-0 outline-none" title="{{ in_array($book->id, $savedIds) ? 'Remove from saved' : 'Save for later' }}">
                            <i class="{{ in_array($book->id, $savedIds) ? 'fas' : 'far' }} fa-bookmark fs-30" style="color:#05653D;"></i>
                        </button>
                        <button type="button" wire:click="toggleFavorite({{ $book->id }})" class="btn btn-sm p-1 border-0" title="{{ in_array($book->id, $favoriteIds) ? 'Remove from favourites' : 'Add to favourites' }}">
                            <i class="{{ in_array($book->id, $favoriteIds) ? 'fas' : 'far' }} fa-heart fs-30" style="color:{{ in_array($book->id, $favoriteIds) ? '#D9A426' : '#05653D' }};"></i>
                        </button>
                    </span>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <p class="text-muted mt-3">
                @if ($filter === 'favorites')
                No favourites yet — tap the heart on any book to keep it here.
                @elseif ($filter === 'saved')
                Nothing saved yet — tap the bookmark on any book to save it for later.
                @else
                No books here yet — check back soon, the library is growing!
                @endif
            </p>
        </div>
        @endforelse
    </div>
    @endif

</div>
@endif
