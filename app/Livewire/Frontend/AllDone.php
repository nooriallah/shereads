<?php

namespace App\Livewire\Frontend;

use App\Models\QuestionnaireResponse;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout("layouts.guest")]
class AllDone extends Component
{
    public function mount()
    {
        // No completed questionnaire in this session → start from the questions.
        $token = session('questionnaire_token');

        $completed = $token && QuestionnaireResponse::where('session_token', $token)
            ->whereNotNull('completed_at')
            ->exists();

        if (! $completed) {
            return $this->redirect(route('question'));
        }
    }

    public function render()
    {
        return view('livewire.frontend.all-done');
    }
}
