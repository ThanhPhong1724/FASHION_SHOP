<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('price', 12, 2); // Price at time of adding to cart
            $table->timestamps();

            $table->index(['cart_id', 'product_variant_id']);
            $table->unique(['cart_id', 'product_variant_id']); // Prevent duplicate variants in same cart
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};