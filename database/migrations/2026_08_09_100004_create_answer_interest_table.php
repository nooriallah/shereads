<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The recommendation bridge: which interests does an answer signal, and how strongly.
        Schema::create('answer_interest', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_option_id')->constrained()->cascadeOnDelete();
            $table->foreignId('interest_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('weight')->default(1);
            $table->timestamps();

            $table->unique(['question_option_id', 'interest_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answer_interest');
    }
};
