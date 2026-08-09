<?php

namespace App\Livewire\Frontend;

use App\Models\Question;
use App\Models\QuestionnaireAnswer;
use App\Models\QuestionnaireResponse;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout("layouts.guest")]
class Questions extends Component
{
    /** Ordered ids of the active questions. */
    public array $questionIds = [];

    /** Index of the question currently on screen. */
    public int $current = 0;

    /** question_id => selected question_option_id */
    public array $answers = [];

    public function mount()
    {
        $this->questionIds = Question::active()->ordered()->pluck('id')->all();

        if (empty($this->questionIds)) {
            return; // view shows an empty state
        }

        // Restore any answers already given in this session (back button, refresh).
        $this->answers = $this->response()
            ->answers()
            ->pluck('question_option_id', 'question_id')
            ->all();

        // Resume at the first unanswered question.
        foreach ($this->questionIds as $index => $questionId) {
            if (! array_key_exists($questionId, $this->answers)) {
                $this->current = $index;

                return;
            }
        }

        // Everything already answered → straight to the All Done page.
        $this->redirect(route('alldone'));
    }

    /**
     * Called when the visitor clicks an answer:
     * save it, then move to the next question (or finish).
     */
    public function selectAnswer(int $optionId)
    {
        $questionId = $this->questionIds[$this->current] ?? null;

        if ($questionId === null) {
            return;
        }

        $question = Question::active()->find($questionId);

        // Only accept an option that really belongs to the current question.
        $option = $question?->activeOptions()->whereKey($optionId)->first();

        if (! $option) {
            return;
        }

        $response = $this->response();

        QuestionnaireAnswer::updateOrCreate(
            [
                'questionnaire_response_id' => $response->id,
                'question_id' => $question->id,
            ],
            [
                'question_option_id' => $option->id,
            ],
        );

        $this->answers[$question->id] = $option->id;

        if ($this->current >= count($this->questionIds) - 1) {
            $response->update(['completed_at' => now()]);

            return $this->redirect(route('alldone'));
        }

        $this->current++;
    }

    /** Back arrow: previous question, or back to the intro page from question 1. */
    public function goBack()
    {
        if ($this->current > 0) {
            $this->current--;

            return;
        }

        return $this->redirect(route('startnow'));
    }

    /**
     * The visitor's questionnaire response record, keyed by a session token
     * so answers survive before the user has an account. At signup the
     * response is attached to the new user via this same token.
     */
    protected function response(): QuestionnaireResponse
    {
        $token = session('questionnaire_token');

        if ($token) {
            $existing = QuestionnaireResponse::where('session_token', $token)->first();

            if ($existing) {
                return $existing;
            }
        }

        $response = QuestionnaireResponse::create([
            'session_token' => (string) Str::uuid(),
        ]);

        session(['questionnaire_token' => $response->session_token]);

        return $response;
    }

    public function render()
    {
        $questionId = $this->questionIds[$this->current] ?? null;

        $question = $questionId
            ? Question::with(['options' => fn ($q) => $q->where('is_active', true)->orderBy('position')])
                ->find($questionId)
            : null;

        return view('livewire.frontend.questions', [
            'question' => $question,
            'total' => count($this->questionIds),
        ]);
    }
}
