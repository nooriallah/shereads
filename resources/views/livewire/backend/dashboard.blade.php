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
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle"
                            style="width:56px;height:56px;background:rgba(5,101,61,.1);">
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
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle"
                            style="width:56px;height:56px;background:rgba(231,185,68,.15);">
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
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle"
                            style="width:56px;height:56px;background:rgba(5,101,61,.1);">
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
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle"
                            style="width:56px;height:56px;background:rgba(231,185,68,.15);">
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
                            <div class="progress-bar progress-animated" role="progressbar"
                                style="width: {{ $publishedPct }}%; height:10px; background:#05653D;"
                                aria-valuenow="{{ $publishedPct }}" aria-valuemin="0" aria-valuemax="100">
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
                            <div class="progress-bar progress-animated" role="progressbar"
                                style="width: {{ $completedPct }}%; height:10px; background:#E7B944;"
                                aria-valuenow="{{ $completedPct }}" aria-valuemin="0" aria-valuemax="100">
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
                                    <div class="w-100 rounded-top"
                                        style="max-width:48px; background:#05653D; opacity:.85; height: {{ max(3, round($week['count'] / $maxSignups * 100)) }}%;"
                                        title="Week of {{ $week['label'] }}: {{ $week['count'] }} signups"></div>
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
                                                    <span @class([
                                                        'badge',
                                                        'badge-success' => $book->status === \App\Models\Book::STATUS_PUBLISHED,
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
    <!-- Readers dashboard section -->
    <div class="container-fluid">

        <div class="row">

            <!-- book colum  -->
            <div class="col col-sm-4 col-md-2">
                <div class="book_wrapper">

                    <img src="/backend/images/books/bookpic.png" class="w-100" alt="Book pic">
                    <h6 class="mt-3 fw-bold">
                        Book Name
                        <br />
                        <span>Author name</span>
                    </h6>

                    <div class="details d-flex justify-content-between">
                        <a href="#" class="btn_read btn">Read Now</a>
                        <a href="" class="btn_fav btn">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M13.8979 2.62371C13.0129 1.72116 12.0276 1.44194 11.1024 1.50967C10.2021 1.57558 9.39138 1.96609 8.79924 2.34171C8.33228 2.63793 7.66761 2.63793 7.20064 2.34171C6.60851 1.9661 5.79777 1.57559 4.89753 1.50969C3.97227 1.44195 2.98701 1.72116 2.10197 2.62371C1.05712 3.68924 0.729309 5.00459 0.860945 6.3351C0.991064 7.65028 1.56795 8.98723 2.33736 10.1719C3.10882 11.3597 4.09428 12.4261 5.08292 13.2005C6.04982 13.9578 7.10185 14.5 7.99997 14.5C8.89809 14.5 9.95011 13.9578 10.917 13.2005C11.9056 12.4261 12.8911 11.3597 13.6626 10.1719C14.432 8.98723 15.0088 7.65028 15.139 6.3351C15.2706 5.00459 14.9428 3.68924 13.8979 2.62371ZM9.3349 3.18615C9.85854 2.85398 10.5067 2.55596 11.1754 2.50701C11.8191 2.45988 12.5128 2.63949 13.1839 3.32386C13.9861 4.14188 14.2517 5.14658 14.1438 6.23665C14.0345 7.34205 13.539 8.5262 12.8239 9.6272C12.1109 10.725 11.1997 11.7088 10.3004 12.4133C9.37927 13.1347 8.55189 13.5 7.99997 13.5C7.44804 13.5 6.62066 13.1347 5.69955 12.4133C4.80018 11.7088 3.88903 10.725 3.176 9.6272C2.46092 8.5262 1.96545 7.34205 1.85609 6.23665C1.74824 5.14658 2.01382 4.14189 2.81597 3.32386C3.48706 2.6395 4.18084 2.4599 4.82452 2.50702C5.49323 2.55597 6.14135 2.85399 6.66499 3.18615C7.45894 3.68978 8.54095 3.68978 9.3349 3.18615Z"
                                    fill="#82B29E" />
                            </svg>
                        </a>
                    </div>
                    <div class="starts mt-1">
                        <svg width="88" height="16" viewBox="0 0 88 16" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M7.30579 2.15256C7.52317 1.47852 8.47683 1.47852 8.69421 2.15256L9.62346 5.03389C9.72081 5.33576 10.0021 5.54012 10.3193 5.53942L13.3467 5.53281C14.055 5.53126 14.3496 6.43824 13.7758 6.85328L11.3226 8.62742C11.0656 8.81329 10.9582 9.14395 11.0568 9.44539L11.9987 12.3226C12.219 12.9957 11.4475 13.5563 10.8754 13.1387L8.43003 11.3539C8.17384 11.1669 7.82616 11.1669 7.56997 11.3539L5.12459 13.1387C4.55253 13.5563 3.78101 12.9957 4.00133 12.3226L4.94316 9.44539C5.04184 9.14395 4.9344 8.81329 4.67739 8.62742L2.22423 6.85328C1.65035 6.43824 1.94505 5.53126 2.65328 5.53281L5.68074 5.53942C5.99792 5.54012 6.27919 5.33576 6.37654 5.03389L7.30579 2.15256Z"
                                fill="#FFD500" />
                            <path
                                d="M25.3058 2.15256C25.5232 1.47852 26.4768 1.47852 26.6942 2.15256L27.6235 5.03389C27.7208 5.33576 28.0021 5.54012 28.3193 5.53942L31.3467 5.53281C32.055 5.53126 32.3496 6.43824 31.7758 6.85328L29.3226 8.62742C29.0656 8.81329 28.9582 9.14395 29.0568 9.44539L29.9987 12.3226C30.219 12.9957 29.4475 13.5563 28.8754 13.1387L26.43 11.3539C26.1738 11.1669 25.8262 11.1669 25.57 11.3539L23.1246 13.1387C22.5525 13.5563 21.781 12.9957 22.0013 12.3226L22.9432 9.44539C23.0418 9.14395 22.9344 8.81329 22.6774 8.62742L20.2242 6.85328C19.6504 6.43824 19.945 5.53126 20.6533 5.53281L23.6807 5.53942C23.9979 5.54012 24.2792 5.33576 24.3765 5.03389L25.3058 2.15256Z"
                                fill="#FFD500" />
                            <path
                                d="M43.3058 2.15256C43.5232 1.47852 44.4768 1.47852 44.6942 2.15256L45.6235 5.03389C45.7208 5.33576 46.0021 5.54012 46.3193 5.53942L49.3467 5.53281C50.055 5.53126 50.3496 6.43824 49.7758 6.85328L47.3226 8.62742C47.0656 8.81329 46.9582 9.14395 47.0568 9.44539L47.9987 12.3226C48.219 12.9957 47.4475 13.5563 46.8754 13.1387L44.43 11.3539C44.1738 11.1669 43.8262 11.1669 43.57 11.3539L41.1246 13.1387C40.5525 13.5563 39.781 12.9957 40.0013 12.3226L40.9432 9.44539C41.0418 9.14395 40.9344 8.81329 40.6774 8.62742L38.2242 6.85328C37.6504 6.43824 37.945 5.53126 38.6533 5.53281L41.6807 5.53942C41.9979 5.54012 42.2792 5.33576 42.3765 5.03389L43.3058 2.15256Z"
                                fill="#FFD500" />
                            <path
                                d="M61.3058 2.15256C61.5232 1.47852 62.4768 1.47852 62.6942 2.15256L63.6235 5.03389C63.7208 5.33576 64.0021 5.54012 64.3193 5.53942L67.3467 5.53281C68.055 5.53126 68.3496 6.43824 67.7758 6.85328L65.3226 8.62742C65.0656 8.81329 64.9582 9.14395 65.0568 9.44539L65.9987 12.3226C66.219 12.9957 65.4475 13.5563 64.8754 13.1387L62.43 11.3539C62.1738 11.1669 61.8262 11.1669 61.57 11.3539L59.1246 13.1387C58.5525 13.5563 57.781 12.9957 58.0013 12.3226L58.9432 9.44539C59.0418 9.14395 58.9344 8.81329 58.6774 8.62742L56.2242 6.85328C55.6504 6.43824 55.945 5.53126 56.6533 5.53281L59.6807 5.53942C59.9979 5.54012 60.2792 5.33576 60.3765 5.03389L61.3058 2.15256Z"
                                fill="#FFD500" />
                            <path
                                d="M79.3058 2.15256C79.5232 1.47852 80.4768 1.47852 80.6942 2.15256L81.6235 5.03389C81.7208 5.33576 82.0021 5.54012 82.3193 5.53942L85.3467 5.53281C86.055 5.53126 86.3496 6.43824 85.7758 6.85328L83.3226 8.62742C83.0656 8.81329 82.9582 9.14395 83.0568 9.44539L83.9987 12.3226C84.219 12.9957 83.4475 13.5563 82.8754 13.1387L80.43 11.3539C80.1738 11.1669 79.8262 11.1669 79.57 11.3539L77.1246 13.1387C76.5525 13.5563 75.781 12.9957 76.0013 12.3226L76.9432 9.44539C77.0418 9.14395 76.9344 8.81329 76.6774 8.62742L74.2242 6.85328C73.6504 6.43824 73.945 5.53126 74.6533 5.53281L77.6807 5.53942C77.9979 5.54012 78.2792 5.33576 78.3765 5.03389L79.3058 2.15256Z"
                                fill="#EDEDED" />
                        </svg>

                    </div>
                </div>
            </div>
        </div>

    </div>
@endif
