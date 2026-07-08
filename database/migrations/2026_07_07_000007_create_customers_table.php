<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->comment('CS PIC')->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 20);
            $table->text('address')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->json('tags')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};