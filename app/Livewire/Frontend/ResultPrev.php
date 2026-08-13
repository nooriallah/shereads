<?php

namespace App\Livewire\Frontend;

use App\Services\RecommendationService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout("layouts.guest")]
class ResultPrev extends Component
{
    /** How many recommendations to show as a preview before signup. */
    public const PREVIEW_COUNT = 3;

    /** How many to compute in total (drives the "+N more" hint). */
    public const TOTAL_COUNT = 12;

    public function render(RecommendationService $recommendations)
    {
        $books = $recommendations->forSessionToken(
            session('questionnaire_token'),
            self::TOTAL_COUNT,
        );

        return view('livewire.frontend.result-prev', [
            'previewBooks' => $books->take(self::PREVIEW_COUNT),
            'hiddenCount' => max(0, $books->count() - self::PREVIEW_COUNT),
        ]);
    }
}
