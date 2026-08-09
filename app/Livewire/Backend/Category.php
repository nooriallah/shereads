<?php

namespace App\Livewire\Backend;

use App\Models\Category as CategoryModel;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout("layouts.app")]
class Category extends Component
{
    // The id of the category currently being edited (null when creating).
    public ?int $categoryId = null;

    public $name = '';
    public $description = '';

    public $edit_category = false;

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($this->categoryId),
            ],
            'description' => ['nullable', 'string'],
        ];
    }

    public function addCategory()
    {
        $this->validate();

        CategoryModel::create([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        $this->resetForm();

        session()->flash('message', 'Category added successfully.');
    }

    public function editCategory($id)
    {
        $category = CategoryModel::find($id);

        if (! $category) {
            session()->flash('error', 'Category not found.');

            return;
        }

        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->description = $category->description;
        $this->edit_category = true;
        $this->dispatch('openEditCategoryModal');
    }

    public function updateCategory()
    {
        $this->validate();

        // Always look the record up by its id — never by name,
        // because the admin may be renaming the category itself.
        $category = CategoryModel::find($this->categoryId);

        if (! $category) {
            session()->flash('error', 'Category not found.');

            return;
        }

        $category->update([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        $this->resetForm();

        session()->flash('message', 'Category updated successfully.');
    }

    public function deleteCategory($id)
    {
        $category = CategoryModel::find($id);

        if (! $category) {
            session()->flash('error', 'Category not found.');

            return;
        }

        $category->delete();

        session()->flash('message', 'Category deleted successfully.');
    }

    protected function resetForm(): void
    {
        $this->categoryId = null;
        $this->name = '';
        $this->description = '';
        $this->edit_category = false;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.backend.category', [
            'categories' => CategoryModel::orderBy('name')->get(),
        ]);
    }
}
