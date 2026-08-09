<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questionnaire_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('questionnaire_response_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_option_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // One answer per question per response (single-choice questionnaire).
            $table->unique(['questionnaire_response_id', 'question_id'], 'qa_response_question_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questionnaire_answers');
    }
};
