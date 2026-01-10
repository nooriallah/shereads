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
    public $show_user_list = false;


    public function showUserForm()
    {
        $this->show_user_list = !$this->show_user_list;
        $this->edit_user = false;
        // Reset all form fields
        $this->full_name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->role = '';
        $this->profile_photo = null;
        // reset([$this->full_name, $this->email, $this->password, $this->password_confirmation, $this->role, $this->profile_photo]);
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
            $userData['profile_photo'] = $this->profile_photo->store('/images/profile-photos', 'public');
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



    // Method for editing user on current form if user exists and click over edit button it should open the same form with data
    public function editUser($id)
    {
        $user = UserModel::find($id);
        if ($user) {
            $this->full_name = $user->full_name;
            $this->email = $user->email;
            $this->role = $user->role;
            $this->edit_user = true;
            $this->dispatch('openEditUserModal');

            $this->edit_user = true;
            $this->show_user_list = false;
        } else {
            session()->flash('error', 'User not found.');
        }
    }


    // Method to update user
    public function updateUser()
    {
        // dd("Update clicked");

        $this->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . UserModel::where('email', $this->email)->value('id'),
            'role' => 'required|string|in:admin,subscriber,editor,visitor',
            'profile_photo' => 'nullable|image|max:1024', // 1MB Max
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        $user = UserModel::where('email', $this->email)->first();
        if ($user) {
            $updateData = [
                'full_name' => $this->full_name,
                'email' => $this->email,
                'role' => $this->role,
            ];
            // check if profile photo is set then update otherwise not 
            if ($this->profile_photo) {
                $updateData['profile_photo'] = $this->profile_photo->store('/backend/images/users', 'public');
            }
            // check if password is set then update otherwise not
            if ($this->password) {
                $updateData['password'] = bcrypt($this->password);
            }

            $user->update($updateData);

            // Reset form fields
            $this->full_name = '';
            $this->email = '';
            $this->role = '';
            $this->profile_photo = null;
            $this->edit_user = false;

            // Show success message
            session()->flash('message', 'User updated successfully.');
        } else {
            session()->flash('error', 'User not found.');
        }
    }

    public function render()
    {
        return view('livewire.backend.user', [
            'users' => UserModel::all(),
        ]);
    }
}
