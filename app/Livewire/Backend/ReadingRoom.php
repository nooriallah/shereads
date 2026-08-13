<?php

namespace App\Livewire\Backend;

use App\Enums\UserRole;
use App\Models\Book;
use App\Models\ReadingProgress;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Renderless;
use Livewire\Component;

#[Layout("layouts.app")]
class ReadingRoom extends Component
{
    public Book $book;

    /** Page to open at (resumed from reading progress). */
    public int $page = 1;

    public function mount(Book $book)
    {
        abort_unless($book->hasContent(), 404);

        $user = Auth::user();
        $isStaff = in_array($user->role, UserRole::adminRoles(), true);

        abort_unless($isStaff || $book->status === Book::STATUS_PUBLISHED, 403);

        $this->book = $book->load('authors');

        $progress = ReadingProgress::firstOrCreate(
            ['user_id' => $user->id, 'book_id' => $book->id],
            ['started_at' => now(), 'last_read_at' => now(), 'current_position' => '1'],
        );

        $this->page = max(1, (int) ($progress->current_position ?: 1));
    }

    /**
     * Called by the PDF.js viewer whenever the reader turns a page.
     * Renderless: persisting progress must never re-render (and reset)
     * the client-side PDF viewer.
     */
    #[Renderless]
    public function savePage(int $page, int $totalPages)
    {
        $page = max(1, $page);
        $totalPages = max(1, $totalPages);
        $page = min($page, $totalPages);

        $percent = (int) min(100, round($page / $totalPages * 100));

        $progress = ReadingProgress::firstOrCreate(
            ['user_id' => Auth::id(), 'book_id' => $this->book->id],
            ['started_at' => now()],
        );

        $progress->fill([
            'current_position' => (string) $page,
            'progress_percent' => $percent,
            'last_read_at' => now(),
        ]);

        if ($page >= $totalPages && ! $progress->completed_at) {
            $progress->completed_at = now();
        }

        $progress->save();

        $this->page = $page;
    }

    public function render()
    {
        return view('livewire.backend.reading-room');
    }
}
