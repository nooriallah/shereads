<?php

namespace App\Livewire\Backend;

use App\Models\CategoryModel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout("layouts.app")]
class Category extends Component
{

    #[Validate()]
    public $name;
    #[Validate()]
    public $description;


    public $edit_category = false;

    public $rules = [
        'name' => 'required|unique:categories,name',
        'description' => 'nullable|string',
    ];

    public function addCategory()
    {
        $this->validate();

        CategoryModel::create([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        $this->name = '';
        $this->description = '';
        if($this->name == '' && $this->description == ''){
            $edit_category = false;
        }

        session()->flash('message', 'Category added successfully.');
    }


    
    public function editCategory($id)
    {
        $category = CategoryModel::find($id);
        if ($category) {
            $this->name = $category->name;
            $this->description = $category->description;
            $this->edit_category = true;
            $this->dispatch('openEditCategoryModal');
        } else {
            session()->flash('error', 'Category not found.');
        }
    }



    public function updateCategory()
    {
        $this->validate([
            'name' => 'required|unique:categories,name,' . CategoryModel::where('name', $this->name)->value('id'),
            'description' => 'nullable|string',
        ]);

        $category = CategoryModel::where('name', $this->name)->first();
        if ($category) {
            $category->update([
                'name' => $this->name,
                'description' => $this->description,
            ]);

            // Reset input fields
            $this->name = '';
            $this->description = '';
            $this->edit_category = false;

            session()->flash('message', 'Category updated successfully.');
        } else {
            session()->flash('error', 'Category not found.');
        }
    }


    public function deleteCategory($id)
    {
        $category = CategoryModel::find($id);
        if ($category) {
            $category->delete();
            session()->flash('message', 'Category deleted successfully.');
        } else {
            session()->flash('error', 'Category not found.');
        }
    }





    public function render()
    {
        return view('livewire.backend.category', [
            'categories' => CategoryModel::all(),
        ]);
    }
}
