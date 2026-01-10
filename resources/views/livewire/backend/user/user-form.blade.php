{{-- User form --}}
<form class="form" enctype="multipart/form-data" wire:submit.prevent={{ $edit_user ? 'updateUser' : 'createUser' }}>
    @csrf
    <div class="row">

        <div class="position-absolute top-0 start-0 w-100 h-100 z-10" wire:loading.flex>
            <div class="loading-model d-flex align-items-center justify-content-center w-100 h-100"
                style="background-color: #3333338c">
                <div class="data-wrapper">
                    <div class="spinner-border"
                        style="width: 8rem; height: 8rem; border-color: #ffffff; border-right-color: transparent;"
                        role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col col-md-5">
            <div class="mb-3">
                <label for="full_name">Full name</label>
                <input type="text" name="full_name" id="full_name" @class([
                    'form-control form-control-lg',
                    'border-danger' => $errors->has('full_name'),
                ])
                    placeholder="Enter full name" wire:model="full_name" />
                @error('full_name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" @class([
                    'form-control form-control-lg',
                    'border-danger' => $errors->has('email'),
                ])
                    placeholder="Enter email address" wire:model="email" />
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password">{{ $edit_user ? 'New Password' : 'Password' }}</label>
                <input type="password" name="password" id="password" @class([
                    'form-control form-control-lg',
                    'border-danger' => $errors->has('password'),
                ])
                    placeholder={{ $edit_user ? 'Enter new password' : 'Password' }} wire:model="password" />
                @error('password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    @class([
                        'form-control form-control-lg',
                        'border-danger' => $errors->has('password_confirmation'),
                    ]) placeholder="Enter password confirmation"
                    wire:model="password_confirmation" />
                @error('password_confirmation')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

        </div>

        <div class="col col-md-5">
            <div class="mb-3">
                <label for="profile_photo">Profile Picture</label>
                {{-- Check if image uploaded show the image 100px otherwise nothing --}}
                @if ($profile_photo)
                    <div class="mb-2">
                        <img src="{{ $profile_photo->temporaryUrl() }}" alt="Profile Photo" width="100px" />
                    </div>
                @endif
                {{-- <h4 wire:loading.flex wire:target='profile_photo'>Loading...</h4> --}}
                <input type="file" accept=".jpg, .jpeg, .png" wire:model="profile_photo"
                    class="form-control form-control-lg" id="profile_photo" />
            </div>

            <div class="mb-3">
                <label for="role">User Rule</label>
                <select name="role" id="role" @class([
                    'form-control form-control-lg',
                    'border-danger' => $errors->has('role'),
                ]) wire:model="role">
                    <option value="">Select Role</option>
                    @foreach (\App\Enums\UserRules::cases() as $role)
                        <option value="{{ $role->value }}">{{ ucfirst($role->value) }}</option>
                    @endforeach
                </select>
                @error('role')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>



        </div>

        <div class="container">
            <div class="mb-3 text-end">
                <button type="submit"
                    class="btn btn-primary py-3 px-5 d-inline-block">{{ $edit_user ? 'Update User' : 'Create User' }}</button>
            </div>
        </div>
        
    </div>
</form>
