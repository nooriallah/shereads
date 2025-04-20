<?php

namespace App\Livewire\Backend;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout("layouts.app")]
class Category extends Component
{
    public function render()
    {
        return view('livewire.backend.category');
    }
}
