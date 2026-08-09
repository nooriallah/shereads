<?php

namespace App\Livewire\Backend;

use App\Enums\UserRole;
use App\Models\User as UserModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout("layouts.app")]
class User extends Component
{
    use WithFileUploads;

    // The id of the user currently being edited (null when creating).
    public ?int $userId = null;

    public $profile_photo;
    public $full_name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $role = '';

    public $edit_user = false;
    public $show_user_list = true;

    /**
     * Validation rules. Defined as a method so the unique rule
     * can ignore the record being edited.
     */
    protected function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->userId),
            ],
            'password' => [$this->edit_user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in(UserRole::values())],
            'profile_photo' => ['nullable', 'image', 'max:1024'], // 1MB max
        ];
    }

    public function showUserForm()
    {
        $this->show_user_list = ! $this->show_user_list;
        $this->edit_user = false;
        $this->resetForm();
    }

    public function createUser()
    {
        $this->validate();

        $userData = [
            'full_name' => $this->full_name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'role' => $this->role,
        ];

        if ($this->profile_photo) {
            $userData['profile_photo'] = $this->storeProfilePhoto();
        }

        UserModel::create($userData);

        $this->resetForm();
        $this->show_user_list = true;

        session()->flash('message', 'User created successfully.');
    }

    public function editUser($id)
    {
        $user = UserModel::find($id);

        if (! $user) {
            session()->flash('error', 'User not found.');

            return;
        }

        $this->userId = $user->id;
        $this->full_name = $user->full_name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->password = '';
        $this->password_confirmation = '';

        $this->edit_user = true;
        $this->show_user_list = false;
    }

    public function updateUser()
    {
        $this->validate();

        // Always look the record up by its id — never by email,
        // because the admin may be changing the email itself.
        $user = UserModel::find($this->userId);

        if (! $user) {
            session()->flash('error', 'User not found.');

            return;
        }

        $user->full_name = $this->full_name;
        $user->email = $this->email;
        $user->role = $this->role;

        if ($this->profile_photo) {
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $user->profile_photo = $this->storeProfilePhoto();
        }

        if ($this->password) {
            $user->password = bcrypt($this->password);
        }

        $user->save();

        $this->resetForm();
        $this->show_user_list = true;
        $this->edit_user = false;

        session()->flash('message', 'User updated successfully.');
    }

    public function deleteUser($id)
    {
        if ((int) $id === Auth::id()) {
            session()->flash('error', 'You cannot delete your own account.');

            return;
        }

        $user = UserModel::find($id);

        if (! $user) {
            session()->flash('error', 'User not found.');

            return;
        }

        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->delete();

        session()->flash('message', 'User deleted successfully.');
    }

    protected function storeProfilePhoto(): string
    {
        $name = str_replace(' ', '_', $this->full_name)
            . '_' . time()
            . '.' . $this->profile_photo->getClientOriginalExtension();

        return $this->profile_photo->storeAs('profiles', $name, 'public');
    }

    protected function resetForm(): void
    {
        $this->userId = null;
        $this->full_name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->role = '';
        $this->profile_photo = null;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.backend.user', [
            'users' => UserModel::latest()->get(),
        ]);
    }
}
