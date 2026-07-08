<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 20);
            $table->text('address')->nullable();
            $table->foreignId('lead_source_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['new', 'contacted', 'qualified', 'converted', 'closed'])->default('new');
            $table->enum('qualification', ['qualified', 'unqualified', 'not_fit'])->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');
            $table->index('qualification');
            $table->index('assigned_to');
            $table->index('lead_source_id');
            $table->index('phone');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};