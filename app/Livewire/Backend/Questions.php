<?php

namespace App\Livewire\Backend;

use App\Models\Interest;
use App\Models\Question;
use App\Models\QuestionOption;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Admin management of the onboarding questionnaire:
 * questions, their answer options, ordering, activation, and the
 * interest signals (with weights) that drive recommendations.
 */
#[Layout("layouts.app")]
class Questions extends Component
{
    /** Which form is open: null | 'question' | 'option' */
    public ?string $formMode = null;

    // ---- Question form ----
    public ?int $questionId = null;
    public string $question_text = '';
    public bool $question_active = true;

    // ---- Option form ----
    public ?int $optionId = null;
    public ?int $optionQuestionId = null;
    public string $option_text = '';
    public bool $option_active = true;

    /** interest_id => weight (0 = no signal) */
    public array $signals = [];

    /*
    |----------------------------------------------------------------
    | Question CRUD
    |----------------------------------------------------------------
    */

    public function newQuestion()
    {
        $this->resetForms();
        $this->formMode = 'question';
    }

    public function editQuestion(int $id)
    {
        $question = Question::find($id);

        if (! $question) {
            session()->flash('error', 'Question not found.');

            return;
        }

        $this->resetForms();
        $this->formMode = 'question';
        $this->questionId = $question->id;
        $this->question_text = $question->question_text;
        $this->question_active = $question->is_active;
    }

    public function saveQuestion()
    {
        $this->validate([
            'question_text' => ['required', 'string', 'max:500'],
        ]);

        if ($this->questionId) {
            Question::whereKey($this->questionId)->update([
                'question_text' => $this->question_text,
                'is_active' => $this->question_active,
            ]);
            session()->flash('message', 'Question updated.');
        } else {
            Question::create([
                'question_text' => $this->question_text,
                'position' => (int) Question::max('position') + 1,
                'is_active' => $this->question_active,
            ]);
            session()->flash('message', 'Question created.');
        }

        $this->resetForms();
    }

    public function toggleQuestion(int $id)
    {
        $question = Question::find($id);

        if ($question) {
            $question->update(['is_active' => ! $question->is_active]);
        }
    }

    public function deleteQuestion(int $id)
    {
        Question::whereKey($id)->delete();

        $this->normalizeQuestionPositions();
        $this->resetForms();

        session()->flash('message', 'Question deleted (its options and collected answers were removed too).');
    }

    public function moveQuestion(int $id, string $direction)
    {
        $ids = Question::orderBy('position')->orderBy('id')->pluck('id')->all();
        $index = array_search($id, $ids, true);

        if ($index === false) {
            return;
        }

        $swapWith = $direction === 'up' ? $index - 1 : $index + 1;

        if ($swapWith < 0 || $swapWith >= count($ids)) {
            return;
        }

        [$ids[$index], $ids[$swapWith]] = [$ids[$swapWith], $ids[$index]];

        foreach ($ids as $position => $questionId) {
            Question::whereKey($questionId)->update(['position' => $position + 1]);
        }
    }

    protected function normalizeQuestionPositions(): void
    {
        foreach (Question::orderBy('position')->orderBy('id')->pluck('id') as $i => $id) {
            Question::whereKey($id)->update(['position' => $i + 1]);
        }
    }

    /*
    |----------------------------------------------------------------
    | Option CRUD (with recommendation signals)
    |----------------------------------------------------------------
    */

    public function newOption(int $questionId)
    {
        if (! Question::whereKey($questionId)->exists()) {
            return;
        }

        $this->resetForms();
        $this->formMode = 'option';
        $this->optionQuestionId = $questionId;
    }

    public function editOption(int $id)
    {
        $option = QuestionOption::with('interests')->find($id);

        if (! $option) {
            session()->flash('error', 'Answer option not found.');

            return;
        }

        $this->resetForms();
        $this->formMode = 'option';
        $this->optionId = $option->id;
        $this->optionQuestionId = $option->question_id;
        $this->option_text = $option->option_text;
        $this->option_active = $option->is_active;

        foreach ($option->interests as $interest) {
            $this->signals[$interest->id] = (int) $interest->pivot->weight;
        }
    }

    public function saveOption()
    {
        $this->validate([
            'option_text' => ['required', 'string', 'max:500'],
        ]);

        if ($this->optionId) {
            $option = QuestionOption::find($this->optionId);

            if (! $option) {
                session()->flash('error', 'Answer option not found.');

                return;
            }

            $option->update([
                'option_text' => $this->option_text,
                'is_active' => $this->option_active,
            ]);
            session()->flash('message', 'Answer option updated.');
        } else {
            $option = QuestionOption::create([
                'question_id' => $this->optionQuestionId,
                'option_text' => $this->option_text,
                'position' => (int) QuestionOption::where('question_id', $this->optionQuestionId)->max('position') + 1,
                'is_active' => $this->option_active,
            ]);
            session()->flash('message', 'Answer option created.');
        }

        // Sync recommendation signals (interest links with weights).
        $sync = [];
        foreach ($this->signals as $interestId => $weight) {
            $weight = (int) $weight;
            if ($weight > 0) {
                $sync[(int) $interestId] = ['weight' => min(5, $weight)];
            }
        }
        $option->interests()->sync($sync);

        $this->resetForms();
    }

    public function toggleOption(int $id)
    {
        $option = QuestionOption::find($id);

        if ($option) {
            $option->update(['is_active' => ! $option->is_active]);
        }
    }

    public function deleteOption(int $id)
    {
        QuestionOption::whereKey($id)->delete();
        $this->resetForms();

        session()->flash('message', 'Answer option deleted.');
    }

    public function moveOption(int $id, string $direction)
    {
        $option = QuestionOption::find($id);

        if (! $option) {
            return;
        }

        $ids = QuestionOption::where('question_id', $option->question_id)
            ->orderBy('position')->orderBy('id')->pluck('id')->all();

        $index = array_search($id, $ids, true);
        $swapWith = $direction === 'up' ? $index - 1 : $index + 1;

        if ($index === false || $swapWith < 0 || $swapWith >= count($ids)) {
            return;
        }

        [$ids[$index], $ids[$swapWith]] = [$ids[$swapWith], $ids[$index]];

        foreach ($ids as $position => $optionId) {
            QuestionOption::whereKey($optionId)->update(['position' => $position + 1]);
        }
    }

    /*
    |----------------------------------------------------------------
    | Shared
    |----------------------------------------------------------------
    */

    public function cancelForm()
    {
        $this->resetForms();
    }

    protected function resetForms(): void
    {
        $this->formMode = null;
        $this->questionId = null;
        $this->question_text = '';
        $this->question_active = true;
        $this->optionId = null;
        $this->optionQuestionId = null;
        $this->option_text = '';
        $this->option_active = true;
        $this->signals = [];
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.backend.questions', [
            'questions' => Question::with(['options.interests'])->ordered()->get(),
            'allInterests' => Interest::orderBy('name')->get(['id', 'name']),
            'parentQuestion' => $this->optionQuestionId ? Question::find($this->optionQuestionId) : null,
        ]);
    }
}
