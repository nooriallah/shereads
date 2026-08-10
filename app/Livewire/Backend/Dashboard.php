<?php

namespace App\Livewire\Backend;

use App\Enums\UserRole;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Interest;
use App\Models\QuestionnaireResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout("layouts.app")]
class Dashboard extends Component
{
    public function render()
    {
        $isAdmin = in_array(Auth::user()->role, UserRole::adminRoles(), true);

        return view('livewire.backend.dashboard', [
            'isAdmin' => $isAdmin,
            ...($isAdmin ? $this->adminStats() : []),
        ]);
    }

    /**
     * All statistics shown on the admin dashboard.
     *
     * @return array<string, mixed>
     */
    protected function adminStats(): array
    {
        $now = now();

        // One query per entity, grouped where a breakdown is needed.
        $booksByStatus = Book::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $usersByRole = User::query()
            ->selectRaw('role, COUNT(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        $totalUsers = (int) $usersByRole->sum();
        $readers = (int) ($usersByRole[UserRole::SUBSCRIBER->value] ?? 0);

        // Questionnaire funnel.
        $totalResponses = QuestionnaireResponse::count();
        $completedResponses = QuestionnaireResponse::whereNotNull('completed_at')->count();

        // Visitors who answered the questionnaire AND registered
        // (their response is attached to a user account at signup).
        $linkedResponses = QuestionnaireResponse::whereNotNull('user_id')->count();

        return [
            // Core counts
            'totalBooks' => (int) $booksByStatus->sum(),
            'publishedBooks' => (int) ($booksByStatus[Book::STATUS_PUBLISHED] ?? 0),
            'draftBooks' => (int) ($booksByStatus[Book::STATUS_DRAFT] ?? 0),
            'archivedBooks' => (int) ($booksByStatus[Book::STATUS_ARCHIVED] ?? 0),
            'totalAuthors' => Author::count(),
            'totalCategories' => Category::count(),
            'totalInterests' => Interest::count(),

            // Users
            'totalUsers' => $totalUsers,
            'totalReaders' => $readers,
            'totalStaff' => $totalUsers - $readers,
            'newUsersThisWeek' => User::where('created_at', '>=', $now->copy()->startOfWeek())->count(),
            'newUsersThisMonth' => User::where('created_at', '>=', $now->copy()->startOfMonth())->count(),

            // Questionnaire
            'totalResponses' => $totalResponses,
            'completedResponses' => $completedResponses,
            'linkedResponses' => $linkedResponses,

            // Trend + recent activity
            'signupsPerWeek' => $this->signupsPerWeek(8),
            'latestBooks' => Book::with('authors')->latest()->take(5)->get(),
            'newestUsers' => User::withExists([
                'questionnaireResponses as completed_questionnaire' => fn ($q) => $q->whereNotNull('completed_at'),
            ])->latest()->take(5)->get(),
        ];
    }

    /**
     * User signups bucketed per week for the last N weeks (DB-agnostic:
     * grouped by day in SQL, bucketed into weeks in PHP).
     *
     * @return array<int, array{label: string, count: int}>
     */
    protected function signupsPerWeek(int $weeks): array
    {
        $start = now()->startOfWeek()->subWeeks($weeks - 1);

        $daily = User::where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $buckets = [];

        for ($i = 0; $i < $weeks; $i++) {
            $weekStart = $start->copy()->addWeeks($i);
            $count = 0;

            for ($d = 0; $d < 7; $d++) {
                $count += (int) ($daily[$weekStart->copy()->addDays($d)->toDateString()] ?? 0);
            }

            $buckets[] = [
                'label' => $weekStart->format('d M'),
                'count' => $count,
            ];
        }

        return $buckets;
    }
}
