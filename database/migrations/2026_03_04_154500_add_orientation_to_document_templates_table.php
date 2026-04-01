<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::table('document_templates', function (Blueprint $col) {
            $col->string('orientation')->default('portrait')->after('entity_type');
        });
    }

    public function down(): void
    {
        Schema::table('document_templates', function (Blueprint $col) {
            $col->dropColumn('orientation');
        });
    }
};
