<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Indexes for dynamic field value lookups
        Schema::table('deal_field_values', function (Blueprint $table) {
            $table->index('static_id', 'dfv_static_id_idx');
        });

        Schema::table('counterparty_field_values', function (Blueprint $table) {
            $table->index('static_id', 'cfv_static_id_idx');
        });

        Schema::table('product_field_values', function (Blueprint $table) {
            $table->index('static_id', 'pfv_static_id_idx');
        });

        // Indexes for activity logs
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index(['loggable_type', 'loggable_id'], 'al_loggable_idx');
            $table->index('action', 'al_action_idx');
        });
    }

    public function down(): void
    {
        Schema::table('deal_field_values', function (Blueprint $table) {
            $table->dropIndex('dfv_static_id_idx');
        });

        Schema::table('counterparty_field_values', function (Blueprint $table) {
            $table->dropIndex('cfv_static_id_idx');
        });

        Schema::table('product_field_values', function (Blueprint $table) {
            $table->dropIndex('pfv_static_id_idx');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex('al_loggable_idx');
            $table->dropIndex('al_action_idx');
        });
    }
};
