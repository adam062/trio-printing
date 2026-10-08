<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('filaments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('spool_price', 10, 2); // e.g. 750 EGP
            $table->decimal('spool_weight_grams', 10, 2)->default(1000); // 1kg
            $table->decimal('cost_per_gram', 10, 4); // spool_price / spool_weight_grams
            $table->string('color_hint')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('filament_id')->nullable()->after('image_path')->constrained('filaments')->nullOnDelete();
        });

        Schema::create('custom_quotes', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number')->unique();
            $table->string('customer_name');
            $table->string('phone', 30);
            $table->string('email')->nullable();
            $table->foreignId('filament_id')->nullable()->constrained('filaments')->nullOnDelete();
            $table->string('filament_name');
            $table->string('quality')->default('Standard 0.20mm');
            $table->string('color')->default('lime');
            $table->string('file_path')->nullable();
            $table->string('original_filename');
            $table->decimal('estimated_weight_grams', 10, 2);
            $table->decimal('multiplier', 5, 2)->default(4);
            $table->decimal('estimated_price', 10, 2);
            $table->text('notes')->nullable();
            $table->string('status')->default('new')->index(); // new, contacted, approved, cancelled
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_quotes');
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['filament_id']);
            $table->dropColumn('filament_id');
        });
        Schema::dropIfExists('filaments');
    }
};
