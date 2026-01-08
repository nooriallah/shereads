<div class="container-fluid position-relative">
    {{-- Stop trying to control. --}}


    @if (Session::has('message'))
        <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
    @elseif (Session::has('error'))
        <div class="alert alert-danger" role="alert">{{ Session::get('error') }}</div>
    @endif          


    <div class="d-flex justify-content-between mb-5 bg-light p-3 rounded">
        <h2>{{ $show_user_list ? 'All user list' : ($edit_user ? 'Edit User' : 'Create New User') }}</h2>
        <button class="btn btn-outline-primary d-inline-block px-4" wire:click="showUserForm">
            {{ $show_user_list ? 'Add new user' : 'All users' }}
        </button>
    </div>

    <div class="mt-5" x-show={{ $show_user_list ? 'hidden' : 'open' }}>
        @include('livewire.backend.user.user-form')
    </div>

    <div class=" mt-5" x-show={{ $show_user_list ? 'open' : 'hidden' }}>
        @include('livewire.backend.user.user-list')
    </div>


</div>
