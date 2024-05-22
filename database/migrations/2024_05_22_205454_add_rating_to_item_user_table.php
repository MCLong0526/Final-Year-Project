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
            $table->decimal('rating', 2, 1)->nullable()->after('place_to_meet');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_user', function (Blueprint $table) {
            $table->dropColumn('rating');
        });
    }
};
