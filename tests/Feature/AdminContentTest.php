<?php

use App\Enums\UserRole;
use App\Livewire\Backend\AuthorComp;
use App\Livewire\Backend\Books;
use App\Livewire\Backend\Interests;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Interest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function admin(): User
{
    return User::factory()->create(['role' => UserRole::ADMIN->value]);
}

/* ---------------- Authorization ---------------- */

it('blocks guests from the admin books page', function () {
    $this->get(route('books'))->assertRedirect(route('login'));
});

it('blocks subscribers from the admin books page', function () {
    $subscriber = User::factory()->create(['role' => UserRole::SUBSCRIBER->value]);

    $this->actingAs($subscriber)->get(route('books'))->assertForbidden();
});

it('lets an admin open books, interests, and authors pages', function () {
    $user = admin();

    $this->actingAs($user)->get(route('books'))->assertOk();
    $this->actingAs($user)->get(route('interests'))->assertOk();
    $this->actingAs($user)->get(route('authors'))->assertOk();
});

/* ---------------- Books ---------------- */

it('creates a book with authors, categories, and interests', function () {
    Storage::fake('public');

    $author = Author::create(['name' => 'Khaled', 'lastname' => 'Hosseini']);
    $category = Category::create(['name' => 'Fiction']);
    $interest = Interest::create(['name' => 'Fiction', 'slug' => 'fiction']);

    Livewire::actingAs(admin())
        ->test(Books::class)
        ->set('title', 'A Thousand Splendid Suns')
        ->set('description', 'A story of two women in Kabul.')
        ->set('language', 'en')
        ->set('publication_year', 2007)
        ->set('pages', 384)
        ->set('status', 'published')
        ->set('cover_image', UploadedFile::fake()->image('cover.jpg'))
        ->set('author_ids', [$author->id])
        ->set('category_ids', [$category->id])
        ->set('interest_ids', [$interest->id])
        ->call('createBook')
        ->assertHasNoErrors();

    $book = Book::first();
    expect($book)->not->toBeNull()
        ->and($book->slug)->toBe('a-thousand-splendid-suns')
        ->and($book->status)->toBe('published')
        ->and($book->authors->pluck('id')->all())->toBe([$author->id])
        ->and($book->categories->pluck('id')->all())->toBe([$category->id])
        ->and($book->interests->pluck('id')->all())->toBe([$interest->id])
        ->and($book->cover_image)->not->toBeNull();

    Storage::disk('public')->assertExists($book->cover_image);
});

it('generates unique slugs for duplicate titles', function () {
    $component = Livewire::actingAs(admin())->test(Books::class);

    $component->set('title', 'My Book')->call('createBook')->assertHasNoErrors();
    $component->set('title', 'My Book')->call('createBook')->assertHasNoErrors();

    expect(Book::pluck('slug')->all())->toBe(['my-book', 'my-book-2']);
});

it('updates a book and its relations', function () {
    $book = Book::create(['title' => 'Old Title', 'slug' => 'old-title']);
    $author = Author::create(['name' => 'New', 'lastname' => 'Author']);

    Livewire::actingAs(admin())
        ->test(Books::class)
        ->call('editBook', $book->id)
        ->assertSet('title', 'Old Title')
        ->set('title', 'New Title')
        ->set('author_ids', [$author->id])
        ->call('updateBook')
        ->assertHasNoErrors();

    $book->refresh();
    expect($book->title)->toBe('New Title')
        ->and($book->slug)->toBe('new-title')
        ->and($book->authors->pluck('id')->all())->toBe([$author->id]);
});

it('toggles publish status from the list', function () {
    $book = Book::create(['title' => 'Draft Book', 'slug' => 'draft-book', 'status' => 'draft']);

    Livewire::actingAs(admin())->test(Books::class)->call('toggleStatus', $book->id);
    expect($book->refresh()->status)->toBe('published');

    Livewire::actingAs(admin())->test(Books::class)->call('toggleStatus', $book->id);
    expect($book->refresh()->status)->toBe('draft');
});

it('soft deletes a book', function () {
    $book = Book::create(['title' => 'Gone', 'slug' => 'gone']);

    Livewire::actingAs(admin())->test(Books::class)->call('deleteBook', $book->id);

    expect(Book::count())->toBe(0)
        ->and(Book::withTrashed()->count())->toBe(1);
});

it('rejects an invalid book status', function () {
    Livewire::actingAs(admin())
        ->test(Books::class)
        ->set('title', 'Bad Status')
        ->set('status', 'not-a-status')
        ->call('createBook')
        ->assertHasErrors(['status']);
});

/* ---------------- Authors ---------------- */

it('creates, updates, and deletes an author', function () {
    $component = Livewire::actingAs(admin())->test(AuthorComp::class);

    $component
        ->set('name', 'Atiq')
        ->set('lastname', 'Rahimi')
        ->set('country', 'Afghanistan')
        ->call('saveAuthor')
        ->assertHasNoErrors();

    $author = Author::first();
    expect($author->full_name)->toBe('Atiq Rahimi');

    $component
        ->call('editAuthor', $author->id)
        ->assertSet('name', 'Atiq')
        ->set('country', 'France')
        ->call('updateAuthor')
        ->assertHasNoErrors();

    expect($author->refresh()->country)->toBe('France');

    $component->call('deleteAuthor', $author->id);
    expect(Author::count())->toBe(0);
});

it('rejects a death date before the birth date', function () {
    Livewire::actingAs(admin())
        ->test(AuthorComp::class)
        ->set('name', 'Test')
        ->set('lastname', 'Author')
        ->set('birthdate', '1990-01-01')
        ->set('deathdate', '1980-01-01')
        ->call('saveAuthor')
        ->assertHasErrors(['deathdate']);
});

/* ---------------- Interests ---------------- */

it('creates, updates, toggles, and deletes an interest', function () {
    $component = Livewire::actingAs(admin())->test(Interests::class);

    $component->set('name', 'Poetry')->call('addInterest')->assertHasNoErrors();

    $interest = Interest::first();
    expect($interest->slug)->toBe('poetry')->and($interest->is_active)->toBeTrue();

    $component->call('editInterest', $interest->id)
        ->set('name', 'Modern Poetry')
        ->call('updateInterest')
        ->assertHasNoErrors();

    expect($interest->refresh()->name)->toBe('Modern Poetry')
        ->and($interest->slug)->toBe('modern-poetry');

    $component->call('toggleActive', $interest->id);
    expect($interest->refresh()->is_active)->toBeFalse();

    $component->call('deleteInterest', $interest->id);
    expect(Interest::count())->toBe(0);
});

it('rejects duplicate interest names', function () {
    Interest::create(['name' => 'History', 'slug' => 'history']);

    Livewire::actingAs(admin())
        ->test(Interests::class)
        ->set('name', 'History')
        ->call('addInterest')
        ->assertHasErrors(['name']);
});
