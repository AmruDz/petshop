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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('qty')->default(0);
            $table->integer('price')->default(0);
            $table->integer('sub_total')->default(0);
            $table->integer('discount')->default(0);
            $table->integer('total')->default(0);
            $table->foreign('transaction_id')->references('id')->on('users');
            $table->foreign('product_id')->references('id')->on('units');
            $table->timestamps();
            $table->index(['transaction_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
