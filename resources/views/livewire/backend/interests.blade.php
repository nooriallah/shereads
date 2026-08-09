<div class="container-fluid">

    @if (Session::has('message'))
        <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
    @elseif (Session::has('error'))
        <div class="alert alert-danger" role="alert">{{ Session::get('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ $edit_interest ? 'Edit interest' : 'Add new interest' }}</h4>
                </div>
                <div class="card-body">
                    <div class="basic-form">
                        <form wire:submit.prevent="{{ $edit_interest ? 'updateInterest' : 'addInterest' }}">
                            @csrf
                            <div class="row align-items-end">
                                <div class="col-md-6 mb-3">
                                    <label for="interest_name">Interest name</label>
                                    <input id="interest_name" @class([
                                        'form-control form-control-lg',
                                        'border-danger' => $errors->has('name'),
                                    ]) type="text"
                                        placeholder="e.g. Personal Development" wire:model="name" />
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="interest_active"
                                            wire:model="is_active">
                                        <label class="form-check-label" for="interest_active">Active</label>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3 text-end">
                                    <input class="btn btn-md btn-success rounded-3" type="submit"
                                        value="{{ $edit_interest ? 'Update' : 'Add' }}" />
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @if ($interests->isEmpty())
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">No Interests Found</h4>
                    </div>
                </div>
            </div>
        @else
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Interest List</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped verticle-middle table-responsive-sm">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Books</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($interests as $interest)
                                        <tr wire:key="interest-{{ $interest->id }}">
                                            <th scope="row">{{ $interest->id }}</th>
                                            <td>{{ $interest->name }}</td>
                                            <td>{{ $interest->books_count }}</td>
                                            <td>
                                                <span @class([
                                                    'badge',
                                                    'bg-success' => $interest->is_active,
                                                    'bg-secondary' => ! $interest->is_active,
                                                ])>{{ $interest->is_active ? 'Active' : 'Inactive' }}</span>
                                            </td>
                                            <td class="d-flex gap-1">
                                                <button class="btn btn-outline-success shadow btn-xs sharp"
                                                    title="{{ $interest->is_active ? 'Deactivate' : 'Activate' }}"
                                                    wire:click="toggleActive({{ $interest->id }})">
                                                    <i class="fa {{ $interest->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                                </button>

                                                <button class="btn btn-primay shadow btn-xs sharp"
                                                    wire:click="editInterest({{ $interest->id }})">
                                                    <i class="fa fa-edit"></i>
                                                </button>

                                                <button wire:click="deleteInterest({{ $interest->id }})"
                                                    wire:confirm="Are you sure? This interest will be removed from all books and answers."
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
