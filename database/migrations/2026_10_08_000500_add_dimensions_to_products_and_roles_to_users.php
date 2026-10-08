<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('dimensions')->nullable()->after('weight_grams'); // e.g. "120 × 85 × 110 mm"
            $table->text('details')->nullable()->after('description'); // Technical specs & print details
            $table->json('gallery_images')->nullable()->after('image_path');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('super_admin')->after('is_admin');
            $table->json('permissions')->nullable()->after('role'); // ['orders', 'products', 'filaments', 'quotes']
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['dimensions', 'details', 'gallery_images']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'permissions']);
        });
    }
};
