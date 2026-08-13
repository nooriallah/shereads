<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Reading content for books.
     *
     * `content_file` — path on the PRIVATE disk (storage/app/private/books).
     * Stored privately so books are only reachable through the
     * authenticated streaming route, never by a direct public URL.
     *
     * `content_type` — 'pdf' for now; column exists so EPUB/HTML
     * can be added later without a schema change.
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('content_file', 2048)->nullable()->after('pages');
            $table->string('content_type', 20)->default('pdf')->after('content_file');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['content_file', 'content_type']);
        });
    }
};
