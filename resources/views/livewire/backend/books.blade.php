<div class="container-fluid position-relative">

    @if (Session::has('message'))
        <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
    @elseif (Session::has('error'))
        <div class="alert alert-danger" role="alert">{{ Session::get('error') }}</div>
    @endif

    {{-- Loading overlay --}}
    <div class="position-absolute top-0 start-0 w-100 h-100 rounded-2" style="z-index: 1040;" wire:loading.flex wire:target="cover_image, createBook, updateBook">
        <div class="loading-model d-flex align-items-center justify-content-center w-100 h-100"
            style="background-color: #3333338c">
            <div class="spinner-border" style="width: 8rem; height: 8rem; border-color: #ffffff; border-right-color: transparent;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between mb-4 p-3 rounded">
        <h2>{{ $show_book_list ? 'All Books' : ($edit_book ? 'Edit Book' : 'Add New Book') }}</h2>
        <button class="btn btn-outline-primary d-inline-block px-4" wire:click="showBookForm">
            {{ $show_book_list ? 'Add new book' : 'All books' }}
        </button>
    </div>

    {{-- ============ FORM ============ --}}
    <div class="mt-3" @if ($show_book_list) style="display: none" @endif>
        <div class="card">
            <div class="card-body">
                <form class="form" enctype="multipart/form-data"
                    wire:submit.prevent="{{ $edit_book ? 'updateBook' : 'createBook' }}">
                    @csrf
                    <div class="row">

                        <div class="col-md-6">

                            <div class="mb-3">
                                <label for="title">Title</label>
                                <input type="text" id="title" @class([
                                    'form-control form-control-lg',
                                    'border-danger' => $errors->has('title'),
                                ]) placeholder="Enter book title" wire:model="title" />
                                @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description">Description</label>
                                <textarea id="description" class="form-control form-control-lg" style="height: 150px;"
                                    placeholder="Enter book description" wire:model="description"></textarea>
                                @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col">
                                    <label for="language">Language</label>
                                    <select id="language" class="form-control form-control-lg" wire:model="language">
                                        @foreach ($languages as $code => $label)
                                            <option value="{{ $code }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('language') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col">
                                    <label for="status">Status</label>
                                    <select id="status" class="form-control form-control-lg" wire:model="status">
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                    @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col">
                                    <label for="publication_year">Publication year</label>
                                    <input type="number" id="publication_year" class="form-control form-control-lg"
                                        placeholder="e.g. 2020" wire:model="publication_year" />
                                    @error('publication_year') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col">
                                    <label for="pages">Pages</label>
                                    <input type="number" id="pages" class="form-control form-control-lg"
                                        placeholder="e.g. 250" wire:model="pages" />
                                    @error('pages') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="cover_image">Cover image</label>
                                @if ($cover_image)
                                    <div class="mb-2">
                                        <img src="{{ $cover_image->temporaryUrl() }}" alt="Cover preview" width="100px" />
                                    </div>
                                @elseif ($existing_cover)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $existing_cover) }}" alt="Current cover" width="100px" />
                                    </div>
                                @endif
                                <input type="file" accept=".jpg, .jpeg, .png" id="cover_image"
                                    class="form-control form-control-lg" wire:model="cover_image" />
                                @error('cover_image') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3" x-data="{ uploading: false, progress: 0 }"
                                x-on:livewire-upload-start="uploading = true"
                                x-on:livewire-upload-finish="uploading = false; progress = 0"
                                x-on:livewire-upload-cancel="uploading = false; progress = 0"
                                x-on:livewire-upload-error="uploading = false"
                                x-on:livewire-upload-progress="progress = $event.detail.progress">

                                <label for="content_file">
                                    Book PDF
                                    <small class="text-muted">(required to publish — max 100MB)</small>
                                </label>

                                @if ($existing_content && ! $content_file)
                                    <div class="mb-2 d-flex align-items-center gap-2">
                                        <span class="badge badge-success">PDF uploaded</span>
                                        <a href="{{ route('book.content', $bookId) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            View current PDF
                                        </a>
                                        <small class="text-muted">Choosing a new file will replace it.</small>
                                    </div>
                                @endif

                                @if ($content_file)
                                    <div class="mb-2">
                                        <span class="badge badge-success">New PDF selected — will be saved with the book</span>
                                    </div>
                                @endif

                                <input type="file" accept="application/pdf,.pdf" id="content_file"
                                    class="form-control form-control-lg" wire:model="content_file" />

                                {{-- Upload progress --}}
                                <div class="progress mt-2" style="height: 8px;" x-show="uploading" x-cloak>
                                    <div class="progress-bar" role="progressbar"
                                        style="background: var(--sr-primary-500, #05653D);"
                                        x-bind:style="'width: ' + progress + '%; background: var(--sr-primary-500, #05653D);'"></div>
                                </div>
                                <small class="text-muted" x-show="uploading" x-cloak>
                                    Uploading… <span x-text="progress"></span>%
                                </small>

                                @error('content_file') <span class="text-danger d-block">{{ $message }}</span> @enderror
                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="mb-3">
                                <label>Authors</label>
                                <div class="border rounded p-3" style="max-height: 180px; overflow-y: auto;">
                                    @forelse ($allAuthors as $author)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $author->id }}"
                                                id="author-{{ $author->id }}" wire:model="author_ids">
                                            <label class="form-check-label" for="author-{{ $author->id }}">
                                                {{ $author->full_name }}
                                            </label>
                                        </div>
                                    @empty
                                        <p class="mb-0">No authors yet — add them on the Authors page first.</p>
                                    @endforelse
                                </div>
                                @error('author_ids') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label>Categories</label>
                                <div class="border rounded p-3" style="max-height: 180px; overflow-y: auto;">
                                    @forelse ($allCategories as $category)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $category->id }}"
                                                id="category-{{ $category->id }}" wire:model="category_ids">
                                            <label class="form-check-label" for="category-{{ $category->id }}">
                                                {{ $category->name }}
                                            </label>
                                        </div>
                                    @empty
                                        <p class="mb-0">No categories yet — add them on the Categories page first.</p>
                                    @endforelse
                                </div>
                                @error('category_ids') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label>Interests <small class="text-muted">(used for recommendations)</small></label>
                                <div class="border rounded p-3" style="max-height: 180px; overflow-y: auto;">
                                    @forelse ($allInterests as $interest)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" value="{{ $interest->id }}"
                                                id="interest-{{ $interest->id }}" wire:model="interest_ids">
                                            <label class="form-check-label" for="interest-{{ $interest->id }}">
                                                {{ $interest->name }}
                                            </label>
                                        </div>
                                    @empty
                                        <p class="mb-0">No interests yet — add them on the Interests page first.</p>
                                    @endforelse
                                </div>
                                @error('interest_ids') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3 text-end mt-4">
                                <button type="submit" class="btn btn-primary py-2 px-4 d-inline-block">
                                    {{ $edit_book ? 'Update Book' : 'Create Book' }}
                                </button>
                            </div>

                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ============ LIST ============ --}}
    <div class="mt-3" @unless ($show_book_list) style="display: none" @endunless>

        <div class="mb-3 col-md-4">
            <input type="search" class="form-control" placeholder="Search books by title..." wire:model.live.debounce.400ms="search">
        </div>

        @if ($books->isEmpty())
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">No Books Found</h4>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped verticle-middle table-responsive-sm">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Cover</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Authors</th>
                                    <th scope="col">Categories</th>
                                    <th scope="col">PDF</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($books as $book)
                                    <tr wire:key="book-{{ $book->id }}">
                                        <th scope="row">{{ $book->id }}</th>
                                        <td>
                                            @if ($book->cover_image)
                                                <img src="{{ asset('storage/' . $book->cover_image) }}" alt="Cover" width="40">
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $book->title }}</td>
                                        <td>{{ $book->authors->pluck('full_name')->join(', ') ?: '—' }}</td>
                                        <td>{{ $book->categories->pluck('name')->join(', ') ?: '—' }}</td>
                                        <td>
                                            @if ($book->hasContent())
                                                <a href="{{ route('book.content', $book->id) }}" target="_blank"
                                                    title="View PDF" class="badge badge-success text-decoration-none">
                                                    <i class="fa fa-file-pdf"></i> View
                                                </a>
                                            @else
                                                <span class="badge badge-secondary" title="No PDF uploaded — cannot be published">
                                                    Missing
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span @class([
                                                'badge',
                                                'bg-success' => $book->status === 'published',
                                                'bg-secondary' => $book->status === 'draft',
                                                'bg-warning' => $book->status === 'archived',
                                            ])>{{ ucfirst($book->status) }}</span>
                                        </td>
                                        <td class="d-flex gap-1">
                                            <button class="btn btn-outline-success shadow btn-xs sharp"
                                                title="{{ $book->status === 'published' ? 'Unpublish' : 'Publish' }}"
                                                wire:click="toggleStatus({{ $book->id }})">
                                                <i class="fa {{ $book->status === 'published' ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                            </button>

                                            <button class="btn btn-primay shadow btn-xs sharp"
                                                wire:click="editBook({{ $book->id }})">
                                                <i class="fa fa-edit"></i>
                                            </button>

                                            <button wire:click="deleteBook({{ $book->id }})" wire:confirm="Are you sure?"
                                                class="btn btn-danger shadow btn-xs sharp">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $books->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

</div>
