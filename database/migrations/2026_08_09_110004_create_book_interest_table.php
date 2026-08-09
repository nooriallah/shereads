<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Which interests a book expresses, and how strongly — used by the recommendation engine.
        Schema::create('book_interest', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->foreignId('interest_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('weight')->default(1);
            $table->timestamps();

            $table->unique(['book_id', 'interest_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_interest');
    }
};
