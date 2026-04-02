<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncData extends Command
{
    protected $signature = 'app:sync-data';
    protected $description = 'Sync metadata from SQLite to PostgreSQL';

    public function handle()
    {
        $this->info('Starting sync from SQLite to PostgreSQL...');

        try {
            // Force SQLite to use the old file, since .env now points to PGSQL
            config(['database.connections.sqlite.database' => database_path('database.sqlite')]);
            
            DB::connection('sqlite')->getPdo();
            DB::connection('pgsql')->getPdo();
        } catch (\Exception $e) {
            $this->error('Connection error: ' . $e->getMessage());
            return 1;
        }

        // 1. Core Metadata
        $this->syncTable('pipelines');
        $this->syncTable('pipeline_stages');
        $this->syncTable('fields_metadata');

        // 2. Main Entities
        $this->syncTable('counterparties');
        $this->syncTable('counterparty_field_values');
        $this->syncTable('products');
        $this->syncTable('product_field_values');
        $this->syncTable('deals');
        $this->syncTable('deal_field_values');
        $this->syncTable('deal_products'); // Pivot table for relations

        $this->resetSequences();

        $this->info('Migration completed successfully! Sequences reset.');
        return 0;
    }

    protected function resetSequences()
    {
        $this->info('Resetting PostgreSQL sequences...');
        
        $tables = [
            'pipelines', 'pipeline_stages', 'fields_metadata', 
            'counterparties', 'counterparty_field_values', 
            'products', 'product_field_values', 
            'deals', 'deal_field_values', 'deal_products'
        ];

        foreach ($tables as $table) {
            $maxId = DB::connection('pgsql')->table($table)->max('id');
            if ($maxId) {
                // PostgreSQL specific sequence reset
                DB::connection('pgsql')->statement("SELECT setval(pg_get_serial_sequence('$table', 'id'), $maxId)");
            }
        }
    }

    protected function syncTable($table)
    {
        $this->info("Syncing table: {$table}");
        
        $data = DB::connection('sqlite')->table($table)->get();
        
        foreach ($data as $item) {
            DB::connection('pgsql')->table($table)->updateOrInsert(
                ['id' => $item->id],
                (array)$item
            );
        }
    }
}
