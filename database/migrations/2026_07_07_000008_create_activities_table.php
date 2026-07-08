<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('activitable'); // activitable_type, activitable_id
            $table->enum('type', ['call', 'whatsapp', 'email', 'meeting', 'note', 'other'])->default('call');
            $table->string('subject')->nullable();
            $table->text('description')->nullable();
            $table->string('duration')->nullable()->comment('5min, 15min, 30min, 1hr, 2hr');
            $table->string('result')->nullable()->comment('Connected, No Answer, etc');
            $table->dateTime('activity_date')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->enum('follow_up_type', ['call', 'whatsapp', 'email', 'meeting'])->nullable();
            $table->text('follow_up_notes')->nullable();
            $table->enum('follow_up_status', ['pending', 'done', 'cancelled'])->default('pending');
            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
            $table->index('follow_up_date');
            $table->index('follow_up_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};