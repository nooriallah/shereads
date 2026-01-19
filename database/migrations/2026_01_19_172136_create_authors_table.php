<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('authors', function (Blueprint $table) {
            $table->id();

            // Properties of book authors
            $table->string('name');
            $table->string('lastname');

            $table->string('website')->nullable();
            $table->string("author_photo")->nullable();

            $table->string('country')->nullable();

            $table->date('birthdate')->nullable();
            $table->string('deathdate')->nullable();

            $table->text('bio')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};
