<div class="container-fluid">
    <!-- row -->
    @if (Session::has('message'))
        <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
    @elseif (Session::has('error'))
        <div class="alert alert-danger" role="alert">{{ Session::get('error') }}</div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Add new category</h4>
                </div>
                <div class="card-body">
                    <div class="basic-form">
                        <form
                            @if ($edit_category) wire:submit.prevent="updateCategory"
                            @else wire:submit.prevent="addCategory" @endif>

                            @csrf
                            <div class="mb-3">
                                <label for="cat_name">Category name</label>
                                <input name="cat_name" id="cat_name" @class([
                                    'form-control form-control-lg',
                                    'border-danger' => $errors->has('name'),
                                ]) type="text"
                                    placeholder="Enter category name" wire:model="name" />
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="cat_desc">Category description</label>
                                <textarea class="form-control form-control-lg mx-3" name="cat_description" id="cat_description" type="text"
                                    placeholder="Enter category description" wire:model="description" style="height: 150px;"></textarea>
                            </div>
                            <div class="d-flex justify-content-end">
                                <input class="btn btn-md btn-success rounded-3" type="submit"
                                    value="{{ $edit_category ? 'Update' : 'Add' }}" />
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>


    <div class="row">
        @if ($categories->isEmpty())
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">No Categories Found</h4>
                    </div>
                </div>
            </div>
        @else
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Category List</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped verticle-middle table-responsive-sm">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Category Name</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Created At</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categories as $category)
                                        <tr>
                                            <th scope="row">{{ $category->id }}</th>
                                            <td>{{ $category->name }}</td>
                                            <td>{{ $category->description }}</td>
                                            <td>{{ $category->created_at->format('d M Y') }}</td>
                                            <td class="d-flex gap-1">

                                                <button class="btn btn-primay shadow btn-xs sharp"
                                                    wire:click="editCategory({{ $category->id }})">
                                                    <i class="fa fa-edit"></i>
                                                </button>

                                                <button wire:click="deleteCategory({{ $category->id }})"
                                                    wire:confirm='Are you sure?'
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
