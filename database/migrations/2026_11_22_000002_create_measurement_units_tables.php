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
        // Units of measure (e.g. piece, kg, meter, cm).
        Schema::create('measurement_units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('symbol');
            // countable, weight, length
            $table->string('type')->default('countable');
            $table->foreignId('base_unit_id')->nullable()->constrained('measurement_units')->nullOnDelete();
            // Conversion factor to the base unit (e.g. 1 m = 100 cm).
            $table->decimal('factor', 12, 6)->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['name', 'type']);
        });

        // Per-product measurement configuration (uncountable products).
        Schema::create('product_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('measurement_unit_id')->constrained('measurement_units')->cascadeOnDelete();
            $table->decimal('min_quantity', 12, 4)->default(0);
            $table->decimal('max_quantity', 12, 4)->nullable();
            $table->decimal('step', 12, 4)->default(1);
            $table->timestamps();

            $table->unique(['product_id', 'measurement_unit_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_measurements');
        Schema::dropIfExists('measurement_units');
    }
};
