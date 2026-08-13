<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Interest;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Demo/dummy data for developing and testing SHEREADS.
 *
 * Run with:  php artisan db:seed --class=DemoDataSeeder
 *
 * Safe to run more than once — everything uses firstOrCreate.
 * Existing questionnaire questions are left untouched; if their answer
 * options are not yet linked to interests, links are created so the
 * recommendation engine has signals to work with.
 *
 * Every published demo book gets a real multi-page PDF generated on the
 * private disk, so the Reading Room is fully testable.
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $interests = $this->seedInterests();
        $categories = $this->seedCategories();
        $authors = $this->seedAuthors();

        $this->seedQuestionnaire($interests);
        $this->seedBooks($interests, $categories, $authors);
        $this->seedDemoReader();

        $this->command->info('Demo data ready.');
        $this->command->info('Demo reader login: reader@shereads.test / password');
    }

    /** @return array<string, Interest> keyed by slug */
    protected function seedInterests(): array
    {
        $names = [
            'Self-Development',
            'Fiction & Stories',
            'Poetry & Literature',
            'Science & Learning',
            'History & Culture',
            'Psychology & Mind',
            'Biography & Inspiration',
            'Education & Study',
        ];

        $interests = [];

        foreach ($names as $name) {
            $slug = Str::slug($name);
            $interests[$slug] = Interest::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'is_active' => true],
            );
        }

        return $interests;
    }

    /** @return array<string, Category> keyed by name */
    protected function seedCategories(): array
    {
        $names = [
            'Fiction', 'Personal Development', 'Poetry', 'Science',
            'History', 'Psychology', 'Biography', 'Education',
        ];

        $categories = [];

        foreach ($names as $name) {
            $categories[$name] = Category::firstOrCreate(
                ['name' => $name],
                ['description' => $name . ' books'],
            );
        }

        return $categories;
    }

    /** @return array<string, Author> keyed by "name lastname" */
    protected function seedAuthors(): array
    {
        $rows = [
            ['Zahra', 'Ahmadi', 'Afghanistan', 'Novelist writing about the lives and dreams of Afghan women.'],
            ['Maryam', 'Karimi', 'Afghanistan', 'Poet and essayist inspired by classical Persian literature.'],
            ['Forugh', 'Rahimi', 'Afghanistan', 'Writes contemporary short stories for young readers.'],
            ['Laila', 'Hashimi', 'Afghanistan', 'Educator and author of study guides for self-learners.'],
            ['Nadia', 'Qaderi', 'Afghanistan', 'Historian focusing on the culture of Khorasan and Central Asia.'],
            ['Sara', 'Noori', 'Afghanistan', 'Psychologist writing accessible books about the mind and healing.'],
            ['Parwana', 'Sultani', 'Afghanistan', 'Biographer telling the stories of pioneering women.'],
            ['Homa', 'Faizi', 'Afghanistan', 'Science communicator making physics and biology fun.'],
            ['Roya', 'Sadat', 'Afghanistan', 'Fiction writer exploring family, courage and hope.'],
            ['Shabnam', 'Azimi', 'Afghanistan', 'Writes practical books on confidence and everyday growth.'],
        ];

        $authors = [];

        foreach ($rows as [$name, $lastname, $country, $bio]) {
            $authors[$name . ' ' . $lastname] = Author::firstOrCreate(
                ['name' => $name, 'lastname' => $lastname],
                ['country' => $country, 'bio' => $bio],
            );
        }

        return $authors;
    }

    /**
     * Seeds questions+options only when none exist. Either way, makes sure
     * every active option signals at least one interest so recommendations work.
     *
     * @param  array<string, Interest>  $interests
     */
    protected function seedQuestionnaire(array $interests): void
    {
        if (Question::count() === 0) {
            $questionnaire = [
                'What do you enjoy reading the most?' => [
                    'Novels and stories' => ['fiction-stories' => 3, 'poetry-literature' => 1],
                    'Poetry' => ['poetry-literature' => 3],
                    'True stories of real people' => ['biography-inspiration' => 3, 'history-culture' => 1],
                    'Books that teach me something' => ['education-study' => 3, 'science-learning' => 2],
                ],
                'What is your main goal when reading?' => [
                    'To grow and improve myself' => ['self-development' => 3, 'psychology-mind' => 1],
                    'To relax and escape' => ['fiction-stories' => 3],
                    'To learn new skills' => ['education-study' => 3, 'science-learning' => 1],
                    'To understand people and culture' => ['history-culture' => 2, 'psychology-mind' => 2],
                ],
                'Which topic sounds most interesting to you?' => [
                    'The human mind and emotions' => ['psychology-mind' => 3],
                    'History and civilizations' => ['history-culture' => 3],
                    'Science and how things work' => ['science-learning' => 3],
                    'Inspiring life stories' => ['biography-inspiration' => 3],
                ],
            ];

            $qPos = 1;

            foreach ($questionnaire as $questionText => $options) {
                $question = Question::create([
                    'question_text' => $questionText,
                    'position' => $qPos++,
                    'is_active' => true,
                ]);

                $oPos = 1;

                foreach ($options as $optionText => $signals) {
                    $option = $question->options()->create([
                        'option_text' => $optionText,
                        'position' => $oPos++,
                        'is_active' => true,
                    ]);

                    foreach ($signals as $slug => $weight) {
                        $option->interests()->attach($interests[$slug]->id, ['weight' => $weight]);
                    }
                }
            }

            return;
        }

        // Questions already exist (created by the admin): don't touch them,
        // but make sure options without interest signals get some, so the
        // recommendation engine produces scored results.
        $interestPool = array_values($interests);
        $i = 0;

        QuestionOption::doesntHave('interests')->get()->each(function (QuestionOption $option) use ($interestPool, &$i) {
            $primary = $interestPool[$i % count($interestPool)];
            $secondary = $interestPool[($i + 3) % count($interestPool)];
            $i++;

            $option->interests()->attach([
                $primary->id => ['weight' => 3],
                $secondary->id => ['weight' => 1],
            ]);
        });
    }

    /**
     * @param  array<string, Interest>  $interests
     * @param  array<string, Category>  $categories
     * @param  array<string, Author>  $authors
     */
    protected function seedBooks(array $interests, array $categories, array $authors): void
    {
        // title => [authors, categories, interests(slug=>weight), language, year, pages, status]
        $books = [
            'The Girl Who Read the Wind' => [['Zahra Ahmadi'], ['Fiction'], ['fiction-stories' => 3, 'self-development' => 1], 'en', 2021, 210],
            'Whispers of the Pomegranate Garden' => [['Roya Sadat'], ['Fiction'], ['fiction-stories' => 3, 'history-culture' => 1], 'fa', 2019, 185],
            'Letters to My Younger Sister' => [['Shabnam Azimi'], ['Personal Development'], ['self-development' => 3, 'biography-inspiration' => 1], 'en', 2022, 150],
            'The Art of Small Steps' => [['Shabnam Azimi'], ['Personal Development'], ['self-development' => 3, 'psychology-mind' => 2], 'en', 2020, 165],
            'Moonlight Over Herat' => [['Maryam Karimi'], ['Poetry'], ['poetry-literature' => 3], 'fa', 2018, 120],
            'Songs of the Silk Road' => [['Maryam Karimi'], ['Poetry'], ['poetry-literature' => 3, 'history-culture' => 2], 'fa', 2021, 140],
            'How Stars Are Born' => [['Homa Faizi'], ['Science'], ['science-learning' => 3], 'en', 2023, 190],
            'The Curious Mind: Everyday Science' => [['Homa Faizi'], ['Science', 'Education'], ['science-learning' => 3, 'education-study' => 2], 'en', 2022, 220],
            'Daughters of Khorasan' => [['Nadia Qaderi'], ['History'], ['history-culture' => 3, 'biography-inspiration' => 1], 'fa', 2017, 260],
            'Caravans and Cities: A Short History' => [['Nadia Qaderi'], ['History'], ['history-culture' => 3], 'en', 2020, 240],
            'Understanding Your Emotions' => [['Sara Noori'], ['Psychology'], ['psychology-mind' => 3, 'self-development' => 2], 'en', 2021, 175],
            'The Healing Notebook' => [['Sara Noori'], ['Psychology', 'Personal Development'], ['psychology-mind' => 3, 'self-development' => 1], 'en', 2023, 160],
            'Twelve Brave Women' => [['Parwana Sultani'], ['Biography'], ['biography-inspiration' => 3, 'history-culture' => 1], 'en', 2019, 230],
            'The Teacher of Kandahar' => [['Parwana Sultani'], ['Biography'], ['biography-inspiration' => 3, 'education-study' => 1], 'fa', 2022, 200],
            'Study Smart: A Guide for Self-Learners' => [['Laila Hashimi'], ['Education'], ['education-study' => 3, 'self-development' => 1], 'en', 2021, 145],
            'English for Beginners at Home' => [['Laila Hashimi'], ['Education'], ['education-study' => 3], 'en', 2023, 180],
            'Short Stories for Long Nights' => [['Forugh Rahimi'], ['Fiction'], ['fiction-stories' => 3, 'poetry-literature' => 1], 'fa', 2020, 155],
            'The Kite Above the Orchard' => [['Forugh Rahimi', 'Roya Sadat'], ['Fiction'], ['fiction-stories' => 3], 'en', 2024, 170],
            'Garden of Words (Draft)' => [['Maryam Karimi'], ['Poetry'], ['poetry-literature' => 2], 'fa', 2024, 90, Book::STATUS_DRAFT],
            'Numbers Around Us (Draft)' => [['Homa Faizi'], ['Science', 'Education'], ['science-learning' => 2], 'en', 2024, 110, Book::STATUS_DRAFT],
        ];

        $adminId = User::whereIn('role', UserRole::adminRoles())->value('id');

        foreach ($books as $title => $data) {
            [$authorNames, $categoryNames, $signals, $language, $year, $pages] = $data;
            $status = $data[6] ?? Book::STATUS_PUBLISHED;

            if (Book::withTrashed()->where('title', $title)->exists()) {
                continue;
            }

            $contentFile = null;

            if ($status === Book::STATUS_PUBLISHED) {
                $contentFile = 'books/demo_' . Str::slug($title) . '.pdf';
                Storage::disk('local')->put($contentFile, $this->makeDemoPdf(
                    $title,
                    implode(', ', $authorNames),
                    max(8, (int) ceil($pages / 20)),
                ));
            }

            $book = Book::create([
                'title' => $title,
                'slug' => Book::uniqueSlug($title),
                'description' => 'A demo book seeded for testing SHEREADS: "' . $title . '" — replace with real content later.',
                'language' => $language,
                'publication_year' => $year,
                'pages' => $pages,
                'status' => $status,
                'content_file' => $contentFile,
                'content_type' => Book::CONTENT_TYPE_PDF,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ]);

            $book->authors()->sync(collect($authorNames)->map(fn ($n) => $authors[$n]->id));
            $book->categories()->sync(collect($categoryNames)->map(fn ($n) => $categories[$n]->id));

            $interestSync = [];
            foreach ($signals as $slug => $weight) {
                $interestSync[$interests[$slug]->id] = ['weight' => $weight];
            }
            $book->interests()->sync($interestSync);
        }
    }

    protected function seedDemoReader(): void
    {
        User::firstOrCreate(
            ['email' => 'reader@shereads.test'],
            [
                'full_name' => 'Demo Reader',
                'password' => Hash::make('password'),
                'role' => UserRole::SUBSCRIBER->value,
            ],
        );
    }

    /**
     * Generates a small but valid multi-page PDF (verified with pdftotext
     * and PDF.js) so the Reading Room has real content to stream.
     */
    protected function makeDemoPdf(string $title, string $author, int $pages): string
    {
        $objects = [];

        $kids = [];
        for ($i = 0; $i < $pages; $i++) {
            $kids[] = (4 + $i * 2) . ' 0 R';
        }

        $objects[1] = "<< /Type /Catalog /Pages 2 0 R >>";
        $objects[2] = "<< /Type /Pages /Kids [" . implode(' ', $kids) . "] /Count {$pages} >>";
        $objects[3] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";

        $esc = fn (string $s) => str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $s);

        for ($i = 0; $i < $pages; $i++) {
            $pageObj = 4 + $i * 2;
            $contentObj = $pageObj + 1;

            $lines = $i === 0
                ? [
                    "BT /F1 26 Tf 72 700 Td ({$esc($title)}) Tj ET",
                    "BT /F1 16 Tf 72 660 Td (by {$esc($author)}) Tj ET",
                    "BT /F1 12 Tf 72 600 Td (This is a demo book generated for SHEREADS testing.) Tj ET",
                    "BT /F1 12 Tf 72 40 Td (Page 1 of {$pages}) Tj ET",
                ]
                : [
                    "BT /F1 18 Tf 72 700 Td (Chapter {$i}) Tj ET",
                    "BT /F1 12 Tf 72 660 Td (Demo content of {$esc($title)} - keep reading!) Tj ET",
                    "BT /F1 12 Tf 72 640 Td (Reading is a window to the world.) Tj ET",
                    "BT /F1 12 Tf 72 40 Td (Page " . ($i + 1) . " of {$pages}) Tj ET",
                ];

            $stream = implode("\n", $lines);
            $objects[$pageObj] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 3 0 R >> >> /Contents {$contentObj} 0 R >>";
            $objects[$contentObj] = "stream:" . $stream;
        }

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        ksort($objects);

        foreach ($objects as $num => $body) {
            $offsets[$num] = strlen($pdf);
            if (str_starts_with($body, 'stream:')) {
                $stream = substr($body, 7);
                $pdf .= "{$num} 0 obj\n<< /Length " . strlen($stream) . " >>\nstream\n{$stream}\nendstream\nendobj\n";
            } else {
                $pdf .= "{$num} 0 obj\n{$body}\nendobj\n";
            }
        }

        $xrefPos = strlen($pdf);
        $count = count($objects) + 1;
        $pdf .= "xref\n0 {$count}\n0000000000 65535 f \n";

        for ($n = 1; $n < $count; $n++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$n]);
        }

        $pdf .= "trailer\n<< /Size {$count} /Root 1 0 R >>\nstartxref\n{$xrefPos}\n%%EOF";

        return $pdf;
    }
}
