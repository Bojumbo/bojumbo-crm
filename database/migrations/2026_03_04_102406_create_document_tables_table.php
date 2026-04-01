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
        Schema::create('document_tables', function (Blueprint $table) {
        $table->id();
        $table->string('name')->unique(); // Тег, наприклад: {{table:contract_products}}
        $table->json('columns'); // Список колонок: [id_поля, заголовок, порядок]
        $table->boolean('show_total')->default(true);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_tables');
    }
};
