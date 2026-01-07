<div class="container-fluid">
    {{-- Stop trying to control. --}}


    <div class="container">
        @if (Session::has('message'))
            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
        @elseif (Session::has('error'))
            <div class="alert alert-danger" role="alert">{{ Session::get('error') }}</div>
        @endif
    </div>


    <div class="container">
        <div class="d-flex justify-content-between mb-5">
            <h2>{{ $show_user_list ? 'All user list' : 'Create New User' }}</h2>
            <button class="btn btn-outline-primary d-inline-block px-4" wire:click="showUserForm">
                {{ $show_user_list ? 'Add new user' : 'All users' }}
            </button>
        </div>
    </div>

    <div class="container mt-5" x-show={{ $show_user_list ? 'hidden' : 'open' }}>
        @include('livewire.backend.user.user-form')
    </div>

    <div class="container mt-5" x-show={{ $show_user_list ? 'open' : 'hidden' }}>
        @include('livewire.backend.user.user-list')
    </div>


</div>
