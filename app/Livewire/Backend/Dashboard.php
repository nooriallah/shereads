<?php

namespace App\Livewire\Backend;

use App\Enums\UserRole;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Interest;
use App\Models\QuestionnaireResponse;
use App\Models\User;
use App\Services\RecommendationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout("layouts.app")]
class Dashboard extends Component
{
    /** How many books to show per shelf on the reader dashboard. */
    public const SHELF_SIZE = 18;

    public const READER_FILTERS = [
        'recommended', 'new', 'top', 'all',
        'favorites', 'saved', 'reading', 'genre', 'author',
    ];

    /** Reader view: which shelf is showing. */
    #[Url]
    public string $filter = 'recommended';

    /** Reader view: genre / author drill-downs. */
    #[Url]
    public ?int $categoryId = null;

    #[Url]
    public ?int $authorId = null;

    public function mount()
    {
        if (! in_array($this->filter, self::READER_FILTERS, true)) {
            $this->filter = 'recommended';
        }
    }

    /*
    |----------------------------------------------------------------
    | Reader actions
    |----------------------------------------------------------------
    */

    public function setFilter(string $filter)
    {
        $this->filter = in_array($filter, self::READER_FILTERS, true) ? $filter : 'recommended';

        if ($this->filter !== 'genre') {
            $this->categoryId = null;
        }
        if ($this->filter !== 'author') {
            $this->authorId = null;
        }
    }

    public function updatedCategoryId($value)
    {
        $this->categoryId = $value ? (int) $value : null;
        $this->filter = $this->categoryId ? 'genre' : 'recommended';
    }

    public function updatedAuthorId($value)
    {
        $this->authorId = $value ? (int) $value : null;
        $this->filter = $this->authorId ? 'author' : 'recommended';
    }

    public function toggleFavorite(int $bookId)
    {
        $book = Book::published()->find($bookId);

        if ($book) {
            Auth::user()->favoriteBooks()->toggle($book->id);
        }
    }

    public function toggleSaved(int $bookId)
    {
        $book = Book::published()->find($bookId);

        if ($book) {
            Auth::user()->savedBooks()->toggle($book->id);
        }
    }

    /*
    |----------------------------------------------------------------
    | Render
    |----------------------------------------------------------------
    */

    public function render(RecommendationService $recommendations)
    {
        $isAdmin = in_array(Auth::user()->role, UserRole::adminRoles(), true);

        return view('livewire.backend.dashboard', [
            'isAdmin' => $isAdmin,
            ...($isAdmin ? $this->adminStats() : $this->readerData($recommendations)),
        ]);
    }

    /*
    |----------------------------------------------------------------
    | Reader dashboard data
    |----------------------------------------------------------------
    */

    /** @return array<string, mixed> */
    protected function readerData(RecommendationService $recommendations): array
    {
        $user = Auth::user();

        $books = collect();
        $inProgress = collect();

        if ($this->filter === 'reading') {
            $inProgress = $user->readingProgress()
                ->with('book.authors')
                ->whereHas('book', fn ($q) => $q->published())
                ->orderByDesc('last_read_at')
                ->get();
        } else {
            $books = $this->shelf($user, $recommendations);
        }

        // Continue Reading strip on the home shelf.
        $continueReading = $this->filter === 'recommended'
            ? $user->readingProgress()
                ->with('book.authors')
                ->whereNull('completed_at')
                ->whereHas('book', fn ($q) => $q->published())
                ->orderByDesc('last_read_at')
                ->limit(4)
                ->get()
            : collect();

        return [
            'books' => $books,
            'inProgress' => $inProgress,
            'continueReading' => $continueReading,
            'favoriteIds' => $user->favoriteBooks()->pluck('books.id')->all(),
            'savedIds' => $user->savedBooks()->pluck('books.id')->all(),
            'allCategories' => Category::orderBy('name')->get(['id', 'name']),
            'allAuthors' => Author::orderBy('name')->get(['id', 'name', 'lastname']),
        ];
    }

    protected function shelf(User $user, RecommendationService $recommendations)
    {
        return match ($this->filter) {
            'recommended' => $recommendations->forUser($user, self::SHELF_SIZE),

            'new' => Book::published()->with('authors')
                ->latest()->limit(self::SHELF_SIZE)->get(),

            // "Top-Rated" per Figma — ranked by how many readers favorited it
            // (real ratings are a future feature).
            'top' => Book::published()->with('authors')
                ->withCount('favoritedBy')
                ->orderByDesc('favorited_by_count')
                ->latest()->limit(self::SHELF_SIZE)->get(),

            'all' => Book::published()->with('authors')
                ->latest()->limit(self::SHELF_SIZE * 2)->get(),

            'favorites' => $user->favoriteBooks()->published()->with('authors')
                ->orderByDesc('favorites.created_at')->get(),

            'saved' => $user->savedBooks()->published()->with('authors')
                ->orderByDesc('saved_books.created_at')->get(),

            'genre' => Book::published()->with('authors')
                ->when($this->categoryId, fn ($q) => $q->whereHas(
                    'categories', fn ($qq) => $qq->whereKey($this->categoryId)
                ))
                ->latest()->limit(self::SHELF_SIZE * 2)->get(),

            'author' => Book::published()->with('authors')
                ->when($this->authorId, fn ($q) => $q->whereHas(
                    'authors', fn ($qq) => $qq->whereKey($this->authorId)
                ))
                ->latest()->limit(self::SHELF_SIZE * 2)->get(),

            default => collect(),
        };
    }

    /*
    |----------------------------------------------------------------
    | Admin dashboard statistics
    |----------------------------------------------------------------
    */

    /** @return array<string, mixed> */
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
