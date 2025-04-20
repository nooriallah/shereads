<?php

namespace App\Livewire\Frontend;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout("layouts.guest")]
class StartNow extends Component
{
    public function render()
    {
        return view('livewire.frontend.start-now');
    }
}
