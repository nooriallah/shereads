<?php

namespace App\Livewire\Backend;

use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\User as UserModel;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;

#[Layout("layouts.app")]
class User extends Component
{
    // User with file upload for profile photo, role selection from enum
    use WithFileUploads;

    #[Validate()]
    public $profile_photo;
    #[Validate()]
    public $full_name;
    #[Validate()]
    public $email;
    #[Validate()]
    public $password;
    #[Validate()]
    public $password_confirmation;
    #[Validate()]
    public $role;

    public $rules = [
        'full_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'role' => 'required|string|in:admin,subscriber,editor,visitor',
        'profile_photo' => 'nullable|image|max:1024', // 1MB Max
    ];

    public $edit_user = false;
    public $show_user_list = true;


    public function showUserForm()
    {
        $this->show_user_list = !$this->show_user_list;
    }


    // Function to create user
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
            $userData['profile_photo_path'] = $this->profile_photo->store('profile-photos', 'public');
        }

        UserModel::create($userData);

        // Reset form fields
        $this->full_name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->role = '';
        $this->profile_photo = null;

        // Check if user added successfully show success message otherwise error
        session()->flash('message', 'User created successfully.');
    }







    public function render()
    {
        return view('livewire.backend.user', [
            'users' => UserModel::all(),
        ]);
    }
}
