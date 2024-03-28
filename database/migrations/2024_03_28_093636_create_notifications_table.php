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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->text('information');
            $table->enum('status', ['Read', 'Unread'])->default('Unread');
            $table->dateTime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
