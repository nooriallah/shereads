<?php

namespace Database\Seeders;

use App\Models\Interest;
use App\Models\Question;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class QuestionnaireSeeder extends Seeder
{
    /**
     * Seeds the onboarding questionnaire.
     *
     * Questions 1–3 come directly from the Figma design.
     * Questions 4–6 are proposed placeholders (the Figma flow shows 6 steps
     * but only screens 1–3 exist) — they can be edited from the admin
     * dashboard later without touching code.
     */
    public function run(): void
    {
        $interests = [
            'Fiction', 'Literature', 'Poetry', 'Fantasy', 'Adventure',
            'History', 'Culture', 'Biography', 'Science', 'Education',
            'Personal Development', 'Self-Help', 'Motivation', 'Psychology',
            'Career', 'Leadership', 'Entrepreneurship', 'Communication',
            'Relationships', 'Health',
        ];

        $interestIds = [];
        foreach ($interests as $name) {
            $interestIds[$name] = Interest::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'is_active' => true],
            )->id;
        }

        // question_text => [option_text => [interest => weight]]
        $questions = [
            'What is your primary goal for reading?' => [
                'Relaxation' => ['Fiction' => 2, 'Poetry' => 1, 'Literature' => 1],
                'Motivation' => ['Motivation' => 3, 'Personal Development' => 2],
                'Learning' => ['Education' => 3, 'Science' => 1],
                'Entertainment' => ['Fiction' => 2, 'Fantasy' => 1, 'Adventure' => 1],
            ],
            'Which genre do you enjoy the most?' => [
                'Fiction' => ['Fiction' => 3, 'Literature' => 1],
                'History' => ['History' => 3, 'Culture' => 1],
                'Self-help' => ['Self-Help' => 3, 'Personal Development' => 2],
                'Fantasy' => ['Fantasy' => 3, 'Fiction' => 1],
            ],
            'What topics are you curious to explore?' => [
                'Personal growth' => ['Personal Development' => 3, 'Motivation' => 1],
                'Adventure' => ['Adventure' => 3, 'Fantasy' => 1],
                'Friendships' => ['Relationships' => 3, 'Communication' => 1],
                'Career advice' => ['Career' => 3, 'Leadership' => 1, 'Entrepreneurship' => 1],
            ],
            'Which area would you most like to improve?' => [
                'Career & work skills' => ['Career' => 3, 'Leadership' => 1],
                'Education & study' => ['Education' => 3],
                'Health & wellbeing' => ['Health' => 3, 'Psychology' => 1],
                'Communication & relationships' => ['Communication' => 2, 'Relationships' => 2],
            ],
            'What kind of stories inspire you most?' => [
                'Real-life stories & biographies' => ['Biography' => 3, 'History' => 1],
                'Poetry & literature' => ['Poetry' => 3, 'Literature' => 2],
                'Science & discovery' => ['Science' => 3, 'Education' => 1],
                'Culture & history' => ['Culture' => 2, 'History' => 2],
            ],
            'What do you want your next book to help you do?' => [
                'Build confidence & motivation' => ['Motivation' => 3, 'Personal Development' => 1],
                'Learn new skills' => ['Education' => 2, 'Career' => 1, 'Self-Help' => 1],
                'Escape into a story' => ['Fiction' => 2, 'Fantasy' => 1, 'Adventure' => 1],
                'Understand people & society' => ['Psychology' => 2, 'Culture' => 1, 'Relationships' => 1],
            ],
        ];

        $questionPosition = 1;
        foreach ($questions as $questionText => $options) {
            $question = Question::firstOrCreate(
                ['question_text' => $questionText],
                ['position' => $questionPosition, 'is_active' => true],
            );

            $optionPosition = 1;
            foreach ($options as $optionText => $signals) {
                $option = $question->options()->firstOrCreate(
                    ['option_text' => $optionText],
                    ['position' => $optionPosition, 'is_active' => true],
                );

                $sync = [];
                foreach ($signals as $interestName => $weight) {
                    $sync[$interestIds[$interestName]] = ['weight' => $weight];
                }
                $option->interests()->syncWithoutDetaching($sync);

                $optionPosition++;
            }

            $questionPosition++;
        }
    }
}
