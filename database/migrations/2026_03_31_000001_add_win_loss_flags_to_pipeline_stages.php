<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pipeline_stages', function (Blueprint $table) {
            $table->boolean('is_won')->default(false);
            $table->boolean('is_lost')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('pipeline_stages', function (Blueprint $table) {
            $table->dropColumn(['is_won', 'is_lost']);
        });
    }
};
