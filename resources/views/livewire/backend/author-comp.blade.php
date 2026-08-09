<div class="container-fluid">

    @if (Session::has('message'))
        <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
    @elseif (Session::has('error'))
        <div class="alert alert-danger" role="alert">{{ Session::get('error') }}</div>
    @endif

    <div class="d-flex justify-content-between mb-4 p-3 rounded">
        <h2>{{ $show_author_list ? 'All Authors' : ($edit_author ? 'Edit Author' : 'Add New Author') }}</h2>
        <button class="btn btn-outline-primary d-inline-block px-4" wire:click="showAuthorForm">
            {{ $show_author_list ? 'Add new author' : 'All authors' }}
        </button>
    </div>

    {{-- ============ FORM ============ --}}
    <div class="row" @if ($show_author_list) style="display: none" @endif>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ $edit_author ? 'Edit author' : 'Add new author' }}</h4>
                </div>
                <div class="card-body">
                    <div class="basic-form">
                        @include("livewire.backend.author.author-form")
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ LIST ============ --}}
    <div class="row" @unless ($show_author_list) style="display: none" @endunless>
        @if ($authors->isEmpty())
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">No Authors Found</h4>
                    </div>
                </div>
            </div>
        @else
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Author List</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped verticle-middle table-responsive-sm">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Photo</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Country</th>
                                        <th scope="col">Website</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($authors as $author)
                                        <tr wire:key="author-{{ $author->id }}">
                                            <th scope="row">{{ $author->id }}</th>
                                            <td>
                                                @if ($author->author_photo && Storage::disk('public')->exists($author->author_photo))
                                                    <img src="{{ asset('storage/' . $author->author_photo) }}"
                                                        alt="Author photo" class="rounded-circle" width="50">
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>{{ $author->full_name }}</td>
                                            <td>{{ $author->country ?? '—' }}</td>
                                            <td>
                                                @if ($author->website)
                                                    <a href="{{ $author->website }}" target="_blank" rel="noopener">Visit</a>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td class="d-flex gap-1">
                                                <button class="btn btn-primay shadow btn-xs sharp"
                                                    wire:click="editAuthor({{ $author->id }})">
                                                    <i class="fa fa-edit"></i>
                                                </button>

                                                <button wire:click="deleteAuthor({{ $author->id }})"
                                                    wire:confirm="Are you sure? Books linked to this author will lose the link."
                                                    class="btn btn-danger shadow btn-xs sharp">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

</div>
