<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('loggable_type'); // Counterparty, Deal, Product
            $table->unsignedBigInteger('loggable_id');
            $table->unsignedInteger('static_id')->nullable(); // ID поля з метаданих
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->string('action'); // created, updated, deleted
            $table->timestamps();

            $table->index(['loggable_type', 'loggable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
