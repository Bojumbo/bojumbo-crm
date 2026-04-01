<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::create('deal_field_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('static_id');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->index(['deal_id', 'static_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deal_field_values');
    }
};
