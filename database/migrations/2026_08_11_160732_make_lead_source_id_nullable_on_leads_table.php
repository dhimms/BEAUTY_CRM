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
        Schema::table('leads', function (Blueprint $table) {
            // Drop the existing foreign key first
            $table->dropForeign(['lead_source_id']);

            // Make the column nullable
            $table->foreignId('lead_source_id')->nullable()->change();

            // Re-add the foreign key with nullOnDelete
            $table->foreign('lead_source_id')
                  ->references('id')
                  ->on('lead_sources')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['lead_source_id']);

            $table->foreignId('lead_source_id')->nullable(false)->change();

            $table->foreign('lead_source_id')
                  ->references('id')
                  ->on('lead_sources')
                  ->cascadeOnDelete();
        });
    }
};
