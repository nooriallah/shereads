<?php

namespace App\Livewire\Backend;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Interest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout("layouts.app")]
class Books extends Component
{
    use WithFileUploads;
    use WithPagination;

    public const LANGUAGES = [
        'en' => 'English',
        'fa' => 'Dari / Farsi',
        'ps' => 'Pashto',
    ];

    // The id of the book currently being edited (null when creating).
    public ?int $bookId = null;

    public $title = '';
    public $description = '';
    public $language = 'en';
    public $publication_year = '';
    public $pages = '';
    public $status = Book::STATUS_DRAFT;
    public $cover_image;          // new upload
    public $existing_cover = null; // path already stored on the book

    public array $author_ids = [];
    public array $category_ids = [];
    public array $interest_ids = [];

    public $edit_book = false;
    public $show_book_list = true;
    public $search = '';

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'language' => ['required', Rule::in(array_keys(self::LANGUAGES))],
            'publication_year' => ['nullable', 'integer', 'min:1000', 'max:' . (int) date('Y')],
            'pages' => ['nullable', 'integer', 'min:1', 'max:65000'],
            'status' => ['required', Rule::in(Book::STATUSES)],
            'cover_image' => ['nullable', 'image', 'max:2048'], // 2MB max
            'author_ids' => ['array'],
            'author_ids.*' => ['integer', 'exists:authors,id'],
            'category_ids' => ['array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'interest_ids' => ['array'],
            'interest_ids.*' => ['integer', 'exists:interests,id'],
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function showBookForm()
    {
        $this->show_book_list = ! $this->show_book_list;
        $this->edit_book = false;
        $this->resetForm();
    }

    public function createBook()
    {
        $data = $this->validate();

        $book = Book::create([
            'title' => $this->title,
            'slug' => Book::uniqueSlug($this->title),
            'description' => $this->description ?: null,
            'language' => $this->language,
            'publication_year' => $this->publication_year ?: null,
            'pages' => $this->pages ?: null,
            'status' => $this->status,
            'cover_image' => $this->cover_image ? $this->storeCover() : null,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        $this->syncRelations($book);

        $this->resetForm();
        $this->show_book_list = true;

        session()->flash('message', 'Book created successfully.');
    }

    public function editBook($id)
    {
        $book = Book::with(['authors', 'categories', 'interests'])->find($id);

        if (! $book) {
            session()->flash('error', 'Book not found.');

            return;
        }

        $this->bookId = $book->id;
        $this->title = $book->title;
        $this->description = $book->description ?? '';
        $this->language = $book->language;
        $this->publication_year = $book->publication_year ?? '';
        $this->pages = $book->pages ?? '';
        $this->status = $book->status;
        $this->existing_cover = $book->cover_image;
        $this->cover_image = null;

        $this->author_ids = $book->authors->pluck('id')->map(fn ($v) => (int) $v)->all();
        $this->category_ids = $book->categories->pluck('id')->map(fn ($v) => (int) $v)->all();
        $this->interest_ids = $book->interests->pluck('id')->map(fn ($v) => (int) $v)->all();

        $this->edit_book = true;
        $this->show_book_list = false;
    }

    public function updateBook()
    {
        $this->validate();

        $book = Book::find($this->bookId);

        if (! $book) {
            session()->flash('error', 'Book not found.');

            return;
        }

        $book->title = $this->title;

        if ($book->isDirty('title')) {
            $book->slug = Book::uniqueSlug($this->title, $book->id);
        }

        $book->description = $this->description ?: null;
        $book->language = $this->language;
        $book->publication_year = $this->publication_year ?: null;
        $book->pages = $this->pages ?: null;
        $book->status = $this->status;
        $book->updated_by = Auth::id();

        if ($this->cover_image) {
            if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $book->cover_image = $this->storeCover();
        }

        $book->save();

        $this->syncRelations($book);

        $this->resetForm();
        $this->show_book_list = true;
        $this->edit_book = false;

        session()->flash('message', 'Book updated successfully.');
    }

    /** Quick publish/unpublish from the list. */
    public function toggleStatus($id)
    {
        $book = Book::find($id);

        if (! $book) {
            session()->flash('error', 'Book not found.');

            return;
        }

        $book->update([
            'status' => $book->status === Book::STATUS_PUBLISHED
                ? Book::STATUS_DRAFT
                : Book::STATUS_PUBLISHED,
            'updated_by' => Auth::id(),
        ]);

        session()->flash('message', $book->status === Book::STATUS_PUBLISHED
            ? 'Book published.'
            : 'Book unpublished.');
    }

    /** Soft delete — the book is archived, not destroyed. */
    public function deleteBook($id)
    {
        $book = Book::find($id);

        if (! $book) {
            session()->flash('error', 'Book not found.');

            return;
        }

        $book->delete();

        session()->flash('message', 'Book deleted successfully.');
    }

    protected function syncRelations(Book $book): void
    {
        $book->authors()->sync($this->author_ids);
        $book->categories()->sync($this->category_ids);
        // Weight defaults to 1; fine-tuning per-book interest weights can come later.
        $book->interests()->sync($this->interest_ids);
    }

    protected function storeCover(): string
    {
        $name = 'cover_' . time() . '_' . uniqid() . '.' . $this->cover_image->getClientOriginalExtension();

        return $this->cover_image->storeAs('covers', $name, 'public');
    }

    protected function resetForm(): void
    {
        $this->bookId = null;
        $this->title = '';
        $this->description = '';
        $this->language = 'en';
        $this->publication_year = '';
        $this->pages = '';
        $this->status = Book::STATUS_DRAFT;
        $this->cover_image = null;
        $this->existing_cover = null;
        $this->author_ids = [];
        $this->category_ids = [];
        $this->interest_ids = [];
        $this->resetValidation();
    }

    public function render()
    {
        $books = Book::with(['authors', 'categories'])
            ->when($this->search, fn ($q) => $q->where('title', 'like', '%' . $this->search . '%'))
            ->latest()
            ->paginate(10);

        return view('livewire.backend.books', [
            'books' => $books,
            'allAuthors' => Author::orderBy('name')->get(),
            'allCategories' => Category::orderBy('name')->get(),
            'allInterests' => Interest::orderBy('name')->get(),
            'languages' => self::LANGUAGES,
            'statuses' => Book::STATUSES,
        ]);
    }
}
