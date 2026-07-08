<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('value', 15, 2)->default(0);
            $table->foreignId('pipeline_stage_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['open', 'won', 'lost'])->default('open');
            $table->foreignId('lost_reason_id')->nullable()->constrained()->nullOnDelete();
            $table->text('lost_notes')->nullable();
            $table->date('expected_close_date')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');
            $table->index('assigned_to');
            $table->index('pipeline_stage_id');
            $table->index('expected_close_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};