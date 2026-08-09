<?php

namespace App\Livewire\Backend;

use App\Models\Interest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout("layouts.app")]
class Interests extends Component
{
    // The id of the interest currently being edited (null when creating).
    public ?int $interestId = null;

    public $name = '';
    public $is_active = true;

    public $edit_interest = false;

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('interests', 'name')->ignore($this->interestId),
            ],
            'is_active' => ['boolean'],
        ];
    }

    public function addInterest()
    {
        $this->validate();

        Interest::create([
            'name' => $this->name,
            'slug' => $this->uniqueSlug($this->name),
            'is_active' => (bool) $this->is_active,
        ]);

        $this->resetForm();

        session()->flash('message', 'Interest added successfully.');
    }

    public function editInterest($id)
    {
        $interest = Interest::find($id);

        if (! $interest) {
            session()->flash('error', 'Interest not found.');

            return;
        }

        $this->interestId = $interest->id;
        $this->name = $interest->name;
        $this->is_active = $interest->is_active;
        $this->edit_interest = true;
    }

    public function updateInterest()
    {
        $this->validate();

        $interest = Interest::find($this->interestId);

        if (! $interest) {
            session()->flash('error', 'Interest not found.');

            return;
        }

        $interest->update([
            'name' => $this->name,
            'slug' => $this->uniqueSlug($this->name, $interest->id),
            'is_active' => (bool) $this->is_active,
        ]);

        $this->resetForm();

        session()->flash('message', 'Interest updated successfully.');
    }

    public function toggleActive($id)
    {
        $interest = Interest::find($id);

        if (! $interest) {
            session()->flash('error', 'Interest not found.');

            return;
        }

        $interest->update(['is_active' => ! $interest->is_active]);
    }

    public function deleteInterest($id)
    {
        $interest = Interest::find($id);

        if (! $interest) {
            session()->flash('error', 'Interest not found.');

            return;
        }

        $interest->delete();

        session()->flash('message', 'Interest deleted successfully.');
    }

    protected function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'interest';
        $slug = $base;
        $suffix = 2;

        while (
            Interest::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $suffix++;
        }

        return $slug;
    }

    protected function resetForm(): void
    {
        $this->interestId = null;
        $this->name = '';
        $this->is_active = true;
        $this->edit_interest = false;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.backend.interests', [
            'interests' => Interest::withCount('books')->orderBy('name')->get(),
        ]);
    }
}
