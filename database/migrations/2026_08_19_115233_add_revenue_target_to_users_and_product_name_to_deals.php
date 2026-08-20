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
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('revenue_target', 15, 2)->default(0)->after('monthly_target')->comment('Target pendapatan bulanan Sales');
        });

        Schema::table('deals', function (Blueprint $table) {
            $table->string('product_name')->nullable()->after('name')->comment('Nama produk yang dibeli saat deal closing (won)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            //
        });
    }
};
