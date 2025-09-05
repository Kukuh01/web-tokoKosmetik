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
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('quantity');

            $table->foreignId('cosmetic_id')->constrained(
                table: 'cosmetics',
                indexName: 'td_cosmetics_id'
            )->cascadeOnDelete();

            $table->foreignId('booking_transaction_id')->constrained(
                table: 'booking_transactions',
                indexName: 'td_booking_transactions_id'
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
        Schema::dropIfExists('transaction_details');
    }
};
