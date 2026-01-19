<table class="table table-bordered table-striped verticle-middle table-responsive-sm">
    <thead>
        <tr>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Pic</th>
            <th scope="col">Full Name</th>
            <th scope="col">Email</th>
            <th scope="col">Role</th>
            <th scope="col">Created date</th>
            <th scope="col">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
            <tr>
                <th scope="row">{{ $user->id }}</th>
                <td>
                    @if ($user->profile_photo)
                        @if (Storage::disk('public')->exists($user->profile_photo))
                            <img src='{{ asset("storage/$user->profile_photo") }}' alt="Profile Picture"
                                class="rounded-circle" width="50">
                        @else
                            <img src="{{ asset('storage/default-profile.png') }}" alt="Default Profile Picture"
                                class="rounded-circle" width="50">
                        @endif
                    @else
                        <img src="{{ asset('storage/default-profile.png') }}" alt="Default Profile Picture"
                            class="rounded-circle" width="50">
                    @endif
                </td>
                <td>{{ $user->full_name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role }}</td>
                <td>{{ $user->created_at->format('d M Y') }}</td>
                <td class="d-flex gap-1">

                    <button class="btn btn-primay shadow btn-xs sharp" wire:click="editUser({{ $user->id }})">
                        <i class="fa fa-edit"></i>
                    </button>

                    <button wire:click="deleteUser({{ $user->id }})" wire:confirm='Are you sure?'
                        class="btn btn-danger shadow btn-xs sharp">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
