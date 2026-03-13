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
        Schema::create('genre_profile', function (Blueprint $table) {
            $table->foreignId('genre_id')
                ->constrained('genres')
                ->cascadeOnDelete();
            $table->foreignId('user_profile_id')
                ->constrained('user_profiles')
                ->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['genre_id', 'user_profile_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('genre_profile');
    }
};
