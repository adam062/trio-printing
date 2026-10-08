<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('color_options')->nullable()->after('dimensions'); // e.g. ["أسود", "أبيض", "برتقالي", "أخضر ليموني"]
            $table->string('color_label')->nullable()->default('اللون المتاح')->after('color_options');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('color', 60)->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['color_options', 'color_label']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
