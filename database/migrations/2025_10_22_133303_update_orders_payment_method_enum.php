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
        Schema::table('orders', function (Blueprint $table) {
            // Cập nhật enum payment_method để bao gồm 'vnpay'
            $table->enum('payment_method', ['cod', 'bank_transfer', 'credit_card', 'momo', 'zalopay', 'vnpay'])->default('cod')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Quay lại enum cũ
            $table->enum('payment_method', ['cod', 'bank_transfer', 'credit_card', 'momo', 'zalopay'])->default('cod')->change();
        });
    }
};
