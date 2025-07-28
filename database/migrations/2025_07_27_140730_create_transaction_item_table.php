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
        Schema::create('transaction_item', function (Blueprint $table) {
             $table->id();
             $table->unsignedBigInteger('transaction_id'); 
            $table->foreign('transaction_id')->references('id')->on('transaction');
            $table->unsignedBigInteger('product_id');
            $table->string('product_name'); // TAMBAHKAN BARIS INI
            $table->integer('quantity');
            $table->integer('subtotal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_item');
    }
};
