<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_tables', function (Blueprint $table) {
            $table->json('styles')->nullable()->after('columns');
        });
    }
    public function down(): void
    {
        Schema::table('document_tables', function (Blueprint $table) {
            $table->dropColumn('styles');
        });
    }
};