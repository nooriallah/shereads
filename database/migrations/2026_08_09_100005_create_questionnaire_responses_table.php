<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questionnaire_responses', function (Blueprint $table) {
            $table->id();
            // Null while the visitor is anonymous; filled at signup.
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            // Held in the visitor's session; used to claim the response after registration.
            $table->uuid('session_token')->unique();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questionnaire_responses');
    }
};
