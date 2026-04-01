<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    /**
     * NUMERIC FIELD-FIRST: Кожне поле має static_id.
     *
     * Діапазони:
     *   1000-1999: Контрагенти
     *   2000-2999: Угоди
     *   3000-3999: Товари
     *   10000+:    Кастомні поля користувача
     */
    public function up(): void
    {
        Schema::create('fields_metadata', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('static_id')->unique()->comment('Permanent numeric ID for templates, e.g. {{1001}}');
            $table->string('entity', 50)->comment('Entity type: counterparty, deal, product');
            $table->string('field_key', 100)->comment('Code key: name, email, phone, etc.');
            $table->string('field_type', 30)->comment('Data type: text, numeric, date, enum, json');
            $table->boolean('is_system')->default(true)->comment('true = built-in, false = user-created');
            $table->string('label_en', 150)->nullable()->comment('English label');
            $table->string('label_uk', 150)->nullable()->comment('Ukrainian label');
            $table->string('label_he', 150)->nullable()->comment('Hebrew label');
            $table->timestamps();

            // Unique pair: one field_key per entity
            $table->unique(['entity', 'field_key'], 'uq_entity_field');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fields_metadata');
    }
};
