<?php

namespace App\Livewire\Backend;

use App\Models\Author;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout("layouts.app")]
class AuthorComp extends Component
{
    use WithFileUploads;

    // The id of the author currently being edited (null when creating).
    public ?int $authorId = null;

    public $author_photo;
    public $existing_photo = null;
    public $name = '';
    public $lastname = '';
    public $website = '';
    public $country = '';
    public $birthdate = '';
    public $deathdate = '';
    public $bio = '';

    public $edit_author = false;
    public $show_author_list = true;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'birthdate' => ['nullable', 'date'],
            'deathdate' => ['nullable', 'date', 'after_or_equal:birthdate'],
            'bio' => ['nullable', 'string'],
            'author_photo' => ['nullable', 'image', 'max:2048'], // 2MB max
        ];
    }

    public function showAuthorForm()
    {
        $this->show_author_list = ! $this->show_author_list;
        $this->edit_author = false;
        $this->resetForm();
    }

    public function saveAuthor()
    {
        $this->validate();

        $data = $this->formData();

        if ($this->author_photo) {
            $data['author_photo'] = $this->storePhoto();
        }

        Author::create($data);

        $this->resetForm();
        $this->show_author_list = true;

        session()->flash('message', 'Author added successfully.');
    }

    public function editAuthor($id)
    {
        $author = Author::find($id);

        if (! $author) {
            session()->flash('error', 'Author not found.');

            return;
        }

        $this->authorId = $author->id;
        $this->name = $author->name;
        $this->lastname = $author->lastname;
        $this->website = $author->website ?? '';
        $this->country = $author->country ?? '';
        $this->birthdate = $author->birthdate?->format('Y-m-d') ?? '';
        $this->deathdate = $author->deathdate?->format('Y-m-d') ?? '';
        $this->bio = $author->bio ?? '';
        $this->existing_photo = $author->author_photo;
        $this->author_photo = null;

        $this->edit_author = true;
        $this->show_author_list = false;
    }

    public function updateAuthor()
    {
        $this->validate();

        $author = Author::find($this->authorId);

        if (! $author) {
            session()->flash('error', 'Author not found.');

            return;
        }

        $data = $this->formData();

        if ($this->author_photo) {
            if ($author->author_photo && Storage::disk('public')->exists($author->author_photo)) {
                Storage::disk('public')->delete($author->author_photo);
            }
            $data['author_photo'] = $this->storePhoto();
        }

        $author->update($data);

        $this->resetForm();
        $this->show_author_list = true;
        $this->edit_author = false;

        session()->flash('message', 'Author updated successfully.');
    }

    public function deleteAuthor($id)
    {
        $author = Author::find($id);

        if (! $author) {
            session()->flash('error', 'Author not found.');

            return;
        }

        if ($author->author_photo && Storage::disk('public')->exists($author->author_photo)) {
            Storage::disk('public')->delete($author->author_photo);
        }

        $author->delete();

        session()->flash('message', 'Author deleted successfully.');
    }

    protected function formData(): array
    {
        return [
            'name' => $this->name,
            'lastname' => $this->lastname,
            'website' => $this->website ?: null,
            'country' => $this->country ?: null,
            'birthdate' => $this->birthdate ?: null,
            'deathdate' => $this->deathdate ?: null,
            'bio' => $this->bio ?: null,
        ];
    }

    protected function storePhoto(): string
    {
        $name = str_replace(' ', '_', $this->name . '_' . $this->lastname)
            . '_' . time()
            . '.' . $this->author_photo->getClientOriginalExtension();

        return $this->author_photo->storeAs('authors', $name, 'public');
    }

    protected function resetForm(): void
    {
        $this->authorId = null;
        $this->name = '';
        $this->lastname = '';
        $this->website = '';
        $this->country = '';
        $this->birthdate = '';
        $this->deathdate = '';
        $this->bio = '';
        $this->author_photo = null;
        $this->existing_photo = null;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.backend.author-comp', [
            'authors' => Author::orderBy('name')->get(),
        ]);
    }
}
