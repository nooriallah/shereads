<?php

namespace App\Livewire\Backend;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout("layouts.app")]
class AuthorComp extends Component
{

    // With upload files class used here
    use \Livewire\WithFileUploads;

    public $authors;
    public $author_photo;
    public $name;
    public $lastname;
    public $website;
    public $country;
    public $birthdate;
    public $deathdate;
    public $bio;

    public $rules = [
        'name' => 'required|string|max:255',
        'lastname' => 'required|string|max:255',

        'website' => 'nullable|url|max:255',
        'country' => 'nullable|string|max:255',

        'birthdate' => 'nullable|date',
        'deathdate' => 'nullable|string|max:255',

        'bio' => 'nullable|string',
        'author_photo' => 'nullable|image|max:2048', // 2MB Max
    ];


    public function render()
    {
        $this->authors = \App\Models\Authors::all();
        return view('livewire.backend.author-comp', [
            'authors' => $this->authors,
        ]);
    }
}
