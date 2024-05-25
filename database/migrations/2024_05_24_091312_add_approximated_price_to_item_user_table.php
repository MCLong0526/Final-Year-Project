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
        Schema::table('item_user', function (Blueprint $table) {
            $table->decimal('approximated_price', 10, 2)->nullable(); // Add nullable if the column can be null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_user', function (Blueprint $table) {
            $table->dropColumn('approximated_price');
        });
    }
};
