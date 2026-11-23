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
        // Pivot: which option values make up each variant (size, color, ...).
        Schema::create('product_variant_option_value', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_variant_id');
            $table->unsignedBigInteger('product_option_value_id');
            $table->timestamps();

            $table->foreign('product_variant_id', 'pviv_variant_fk')
                ->references('id')->on('product_variants')
                ->cascadeOnDelete();
            $table->foreign('product_option_value_id', 'pviv_optval_fk')
                ->references('id')->on('product_option_values')
                ->cascadeOnDelete();

            $table->unique(['product_variant_id', 'product_option_value_id'], 'pviv_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variant_option_value');
    }
};
