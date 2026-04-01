<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::create('counterparties', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['individual', 'company'])->default('individual');
            $table->timestamps();
            $table->softDeletes(); // Notion Style: дані потрапляють у кошик
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counterparties');
    }
};
