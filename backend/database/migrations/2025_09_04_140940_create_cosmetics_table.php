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
        Schema::create('cosmetics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('thumbnail');
            $table->text('about');
            $table->boolean('is_popular');
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('stock');
            $table->foreignId('brand_id')->constrained(
                table: 'brands',
                indexName: 'cosmetic_brand_id'
            )->cascadeOnDelete();
            $table->foreignId('category_id')->constrained(
                table: 'categories',
                indexName: 'cosmetic_category_id'
            )->cascadeOnDelete();
            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cosmetics');
    }
};
