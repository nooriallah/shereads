<?php

namespace App\Livewire\Frontend;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout("layouts.guest")]
class ResultPrev extends Component
{
    public function render()
    {
        return view('livewire.frontend.result-prev');
    }
}
