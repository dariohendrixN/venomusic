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
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('qobuz_url')->nullable();
            $table->string('bandcamp_url')->nullable();
            $table->string('deezer_url')->nullable();
            $table->string('soundcloud_url')->nullable();
            $table->string('amazon_music_url')->nullable();
            $table->string('youtube_music_url')->nullable();
            $table->string('apple_music_url')->nullable();
            $table->string('spotify_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn('qobuz_url');
            $table->dropColumn('bandcamp_url');
            $table->dropColumn('deezer_url');
            $table->dropColumn('soundcloud_url');
            $table->dropColumn('amazon_music_url');
            $table->dropColumn('youtube_music_url');
            $table->dropColumn('apple_music_url');
            $table->dropColumn('spotify_url');
        });
    }
};
