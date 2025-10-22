<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Xóa dữ liệu cũ để tránh conflict
        DB::table('addresses')->delete();
        
        Schema::table('addresses', function (Blueprint $table) {
            // Cập nhật cột type để chấp nhận 'shipping' và 'billing'
            $table->enum('type', ['shipping', 'billing'])->default('shipping')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            // Quay lại enum cũ
            $table->enum('type', ['home', 'office', 'other'])->default('home')->change();
        });
    }
};
