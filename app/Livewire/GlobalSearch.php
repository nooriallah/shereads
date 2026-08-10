<?php

namespace App\Livewire;

use App\Enums\UserRole;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Interest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Header search box: searches across the platform's data.
 *
 * Admins search everything (books in any status, authors, categories,
 * interests, users). Readers only search published content — never users.
 */
class GlobalSearch extends Component
{
    /** Minimum characters before we hit the database. */
    public const MIN_LENGTH = 2;

    /** Max results shown per group. */
    public const PER_GROUP = 5;

    public string $query = '';

    public function render()
    {
        $term = trim($this->query);
        $isAdmin = in_array(Auth::user()->role, UserRole::adminRoles(), true);

        $results = mb_strlen($term) >= self::MIN_LENGTH
            ? $this->search($term, $isAdmin)
            : collect();

        return view('livewire.global-search', [
            'results' => $results,
            'isAdmin' => $isAdmin,
            'ready' => mb_strlen($term) >= self::MIN_LENGTH,
            'hasResults' => $results->contains(fn ($group) => $group->isNotEmpty()),
        ]);
    }

    /**
     * @return \Illuminate\Support\Collection<string, \Illuminate\Support\Collection>
     */
    protected function search(string $term, bool $isAdmin)
    {
        $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $term) . '%';

        $results = collect([
            'books' => Book::query()
                ->when(! $isAdmin, fn ($q) => $q->published())
                ->where(fn ($q) => $q
                    ->where('title', 'like', $like)
                    ->orWhereHas('authors', fn ($qq) => $qq
                        ->where('name', 'like', $like)
                        ->orWhere('lastname', 'like', $like)))
                ->with('authors')
                ->limit(self::PER_GROUP)
                ->get(),

            'authors' => Author::query()
                ->where(fn ($q) => $q
                    ->where('name', 'like', $like)
                    ->orWhere('lastname', 'like', $like))
                ->limit(self::PER_GROUP)
                ->get(),

            'categories' => Category::where('name', 'like', $like)
                ->limit(self::PER_GROUP)
                ->get(),
        ]);

        if ($isAdmin) {
            $results->put('interests', Interest::where('name', 'like', $like)
                ->limit(self::PER_GROUP)
                ->get());

            $results->put('users', User::query()
                ->where(fn ($q) => $q
                    ->where('full_name', 'like', $like)
                    ->orWhere('email', 'like', $like))
                ->limit(self::PER_GROUP)
                ->get());
        }

        return $results;
    }
}
