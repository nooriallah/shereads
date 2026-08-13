<?php

namespace App\Services;

use App\Models\Book;
use App\Models\QuestionnaireResponse;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * The core SHEREADS recommendation engine.
 *
 * Entirely database-driven — no hard-coded rules:
 *
 *   score(book) = Σ over the user's selected answer options of
 *                 answer_interest.weight × book_interest.weight
 *                 (summed where the option's interest matches the book's)
 *
 * Admins reshape recommendations from the dashboard by editing
 * option↔interest and book↔interest links and weights — zero code changes.
 *
 * Only published books are ever recommended. When there is no completed
 * questionnaire (or no scored matches), we fall back to newest published
 * books so the reader never sees an empty shelf.
 */
class RecommendationService
{
    /**
     * Recommendations for a (possibly anonymous) questionnaire response.
     */
    public function forResponse(?QuestionnaireResponse $response, int $limit = 12): Collection
    {
        if (! $response) {
            return $this->fallback($limit);
        }

        $optionIds = $response->answers()->pluck('question_option_id');

        return $this->scoreByOptions($optionIds->all(), $limit);
    }

    /**
     * Recommendations for a logged-in reader — uses her latest
     * completed questionnaire response.
     */
    public function forUser(User $user, int $limit = 12): Collection
    {
        $response = $user->questionnaireResponses()
            ->whereNotNull('completed_at')
            ->latest()
            ->first();

        return $this->forResponse($response, $limit);
    }

    /**
     * Recommendations for the current session's anonymous visitor
     * (used on the results preview page before signup).
     */
    public function forSessionToken(?string $token, int $limit = 12): Collection
    {
        $response = $token
            ? QuestionnaireResponse::where('session_token', $token)
                ->whereNotNull('completed_at')
                ->latest()
                ->first()
            : null;

        return $this->forResponse($response, $limit);
    }

    /**
     * @param  array<int, int>  $optionIds
     */
    protected function scoreByOptions(array $optionIds, int $limit): Collection
    {
        if (empty($optionIds)) {
            return $this->fallback($limit);
        }

        // Aggregate scores in a derived table so the GROUP BY only touches
        // book_id + the aggregate — strict-mode (ONLY_FULL_GROUP_BY) safe.
        $scores = DB::table('book_interest')
            ->join('answer_interest', 'answer_interest.interest_id', '=', 'book_interest.interest_id')
            ->whereIn('answer_interest.question_option_id', $optionIds)
            ->groupBy('book_interest.book_id')
            ->select('book_interest.book_id')
            ->selectRaw('SUM(answer_interest.weight * book_interest.weight) as score');

        $books = Book::query()
            ->published()
            ->joinSub($scores, 'scores', fn ($join) => $join->on('scores.book_id', '=', 'books.id'))
            ->select('books.*', 'scores.score as recommendation_score')
            ->orderByDesc('recommendation_score')
            ->orderByDesc('books.created_at')
            ->with('authors')
            ->limit($limit)
            ->get();

        return $books->isEmpty() ? $this->fallback($limit) : $books;
    }

    /** Newest published books — shown when scoring returns nothing. */
    protected function fallback(int $limit): Collection
    {
        return Book::published()
            ->with('authors')
            ->latest()
            ->limit($limit)
            ->get();
    }
}
