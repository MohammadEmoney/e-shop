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
        // Attribute definitions (e.g. color, size, material) for products.
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            // select, color, text
            $table->string('type')->default('select');
            $table->boolean('is_filterable')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Allowed values for each attribute (e.g. red, M, cotton).
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_attribute_id')->constrained('product_attributes')->cascadeOnDelete();
            $table->string('value');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['product_attribute_id', 'value']);
        });

        // Pivot: which attribute+value dimensions a product has.
        // Constraint names are kept short (<64 chars) for MySQL.
        Schema::create('product_attribute_value_product', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_attribute_value_id');
            $table->timestamps();

            $table->foreign('product_id', 'pavp_product_fk')
                ->references('id')->on('products')
                ->cascadeOnDelete();
            $table->foreign('product_attribute_value_id', 'pavp_attrval_fk')
                ->references('id')->on('product_attribute_values')
                ->cascadeOnDelete();

            $table->unique(['product_id', 'product_attribute_value_id'], 'product_attr_value_unique');
        });

        // Pivot: which attribute+value combos make up each variant.
        Schema::create('product_variant_attribute_value', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_variant_id');
            $table->unsignedBigInteger('product_attribute_value_id');
            $table->timestamps();

            $table->foreign('product_variant_id', 'pvav_variant_fk')
                ->references('id')->on('product_variants')
                ->cascadeOnDelete();
            $table->foreign('product_attribute_value_id', 'pvav_attrval_fk')
                ->references('id')->on('product_attribute_values')
                ->cascadeOnDelete();

            $table->unique(['product_variant_id', 'product_attribute_value_id'], 'variant_attr_value_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variant_attribute_value');
        Schema::dropIfExists('product_attribute_value_product');
        Schema::dropIfExists('product_attribute_values');
        Schema::dropIfExists('product_attributes');
    }
};
