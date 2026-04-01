<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::create('counterparty_field_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('counterparty_id')->constrained()->onDelete('cascade');

            // Зв'язок нашого значення зі словником через static_id
            $table->unsignedInteger('static_id');
            $table->text('value')->nullable();

            $table->timestamps();

            // Індекси для швидкого пошуку
            $table->index(['counterparty_id', 'static_id']);

            // Зокрема для Google Docs інтеграції - швидкий пошук по ID поля
            $table->index('static_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counterparty_field_values');
    }
};
