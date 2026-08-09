<?php

use App\Livewire\Frontend\Questions;
use App\Models\Question;
use App\Models\QuestionnaireResponse;
use Database\Seeders\QuestionnaireSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(QuestionnaireSeeder::class);
});

it('shows the first question on the questions page', function () {
    $first = Question::active()->ordered()->first();

    $this->get(route('question'))
        ->assertOk()
        ->assertSee($first->question_text);
});

it('advances to the next question when an answer is clicked', function () {
    $questions = Question::active()->ordered()->with('options')->get();

    Livewire::test(Questions::class)
        ->assertSet('current', 0)
        ->call('selectAnswer', $questions[0]->options->first()->id)
        ->assertSet('current', 1)
        ->assertSee($questions[1]->question_text);
});

it('stores the answer in the database', function () {
    $questions = Question::active()->ordered()->with('options')->get();
    $option = $questions[0]->options->first();

    Livewire::test(Questions::class)->call('selectAnswer', $option->id);

    expect(QuestionnaireResponse::count())->toBe(1);

    $response = QuestionnaireResponse::first();
    expect($response->answers)->toHaveCount(1)
        ->and($response->answers->first()->question_option_id)->toBe($option->id)
        ->and($response->completed_at)->toBeNull();
});

it('rejects an option that does not belong to the current question', function () {
    $questions = Question::active()->ordered()->with('options')->get();
    $foreignOption = $questions[2]->options->first();

    Livewire::test(Questions::class)
        ->call('selectAnswer', $foreignOption->id)
        ->assertSet('current', 0);

    expect(QuestionnaireResponse::withCount('answers')->first()?->answers_count ?? 0)->toBe(0);
});

it('completes the questionnaire and redirects to the all done page', function () {
    $questions = Question::active()->ordered()->with('options')->get();

    $component = Livewire::test(Questions::class);

    foreach ($questions as $question) {
        $component->call('selectAnswer', $question->options->first()->id);
    }

    $component->assertRedirect(route('alldone'));

    $response = QuestionnaireResponse::first();
    expect($response->completed_at)->not->toBeNull()
        ->and($response->answers)->toHaveCount($questions->count());
});

it('goes back to the previous question with the back arrow', function () {
    $questions = Question::active()->ordered()->with('options')->get();

    Livewire::test(Questions::class)
        ->call('selectAnswer', $questions[0]->options->first()->id)
        ->assertSet('current', 1)
        ->call('goBack')
        ->assertSet('current', 0);
});

it('redirects back to the intro page when going back from the first question', function () {
    Livewire::test(Questions::class)
        ->call('goBack')
        ->assertRedirect(route('startnow'));
});

it('lets the visitor change a previous answer', function () {
    $questions = Question::active()->ordered()->with('options')->get();
    [$optionA, $optionB] = [$questions[0]->options[0], $questions[0]->options[1]];

    Livewire::test(Questions::class)
        ->call('selectAnswer', $optionA->id)
        ->call('goBack')
        ->call('selectAnswer', $optionB->id);

    $response = QuestionnaireResponse::first();
    expect($response->answers()->where('question_id', $questions[0]->id)->first()->question_option_id)
        ->toBe($optionB->id)
        ->and($response->answers()->count())->toBe(1);
});

it('resumes at the first unanswered question within the same session', function () {
    $questions = Question::active()->ordered()->with('options')->get();

    Livewire::test(Questions::class)
        ->call('selectAnswer', $questions[0]->options->first()->id)
        ->call('selectAnswer', $questions[1]->options->first()->id);

    // A fresh visit in the same session resumes at question 3.
    Livewire::test(Questions::class)->assertSet('current', 2);
});

it('redirects to the questions page when visiting all done without finishing', function () {
    $this->get(route('alldone'))->assertRedirect(route('question'));
});
