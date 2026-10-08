<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('filaments', function (Blueprint $table) {
            $table->json('available_colors')->nullable()->after('color_hint');
        });
    }

    public function down(): void
    {
        Schema::table('filaments', function (Blueprint $table) {
            $table->dropColumn('available_colors');
        });
    }
};
