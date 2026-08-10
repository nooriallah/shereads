@if ($users->isEmpty())
    <div class="card">
        <div class="card-body">
            <p class="text-muted mb-0">
                No users found{{ $search ? ' for "' . $search . '"' : '' }}.
            </p>
        </div>
    </div>
@else
    <table class="table table-bordered table-striped verticle-middle table-responsive-sm">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Pic</th>
                <th scope="col">Full Name</th>
                <th scope="col">Email</th>
                <th scope="col">Role</th>
                <th scope="col">Questionnaire</th>
                <th scope="col">Registered</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <th scope="row">{{ $user->id }}</th>
                    <td>
                        <img src="{{ $user->profile_photo_url }}" alt="Profile Picture"
                            class="rounded-circle" width="50" height="50" style="object-fit: cover;">
                    </td>
                    <td>{{ $user->full_name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @php($roleEnum = \App\Enums\UserRole::tryFrom($user->role))
                        <span @class([
                            'badge',
                            'light badge-success' => $user->role === \App\Enums\UserRole::SUBSCRIBER->value,
                            'light badge-primary' => $user->role !== \App\Enums\UserRole::SUBSCRIBER->value,
                        ])>{{ $roleEnum?->label() ?? ucfirst($user->role) }}</span>
                    </td>
                    <td>
                        @if ($user->completed_questionnaire)
                            <span class="badge light badge-success">Completed</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
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

    <div class="mt-3">
        {{ $users->links() }}
    </div>
@endif
