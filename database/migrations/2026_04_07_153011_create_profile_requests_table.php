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
        Schema::create('profile_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_profile_id')
                ->constrained('user_profiles')
                ->cascadeOnDelete();            
            $table->foreignId('receiver_profile_id')
                ->constrained('user_profiles')                
                ->cascadeOnDelete();
            $table->string('request_type');
            $table->string('status')
                ->default('pending');
            $table->string('subject')
                ->nullable();
            $table->text('message')
                ->nullable();
            $table->timestamp('requested_date')
                ->nullable();
            $table->timestamp('answered_at')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_requests');
    }
};
