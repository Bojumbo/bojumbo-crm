<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StructuralSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Очищуємо старі метадані перед заповненням (опціонально, щоб уникнути дублікатів)
        DB::statement('TRUNCATE TABLE fields_metadata CASCADE');
        DB::statement('TRUNCATE TABLE pipeline_stages CASCADE');
        DB::statement('TRUNCATE TABLE pipelines CASCADE');

        // 2. Вставляємо поля (21 поле з твого локульного JSON)
        $fields = [
            ['static_id' => 1001, 'entity' => 'counterparty', 'field_key' => 'name', 'field_type' => 'text', 'is_system' => true, 'label_en' => 'Name/Company', 'label_uk' => 'Назва/Ім\'я', 'label_he' => 'שם/חברה'],
            ['static_id' => 1002, 'entity' => 'counterparty', 'field_key' => 'email', 'field_type' => 'text', 'is_system' => true, 'label_en' => 'Email', 'label_uk' => 'Email', 'label_he' => 'אימייל'],
            ['static_id' => 1003, 'entity' => 'counterparty', 'field_key' => 'phone', 'field_type' => 'text', 'is_system' => true, 'label_en' => 'Phone', 'label_uk' => 'Телефон', 'label_he' => 'טלפון'],
            ['static_id' => 1004, 'entity' => 'counterparty', 'field_key' => 'type', 'field_type' => 'enum', 'is_system' => true, 'label_en' => 'Type', 'label_uk' => 'Тип', 'label_he' => 'סוג'],
            ['static_id' => 2001, 'entity' => 'deal', 'field_key' => 'title', 'field_type' => 'text', 'is_system' => true, 'label_en' => 'Deal Title', 'label_uk' => 'Назва угоди', 'label_he' => 'שם העסקה'],
            ['static_id' => 2002, 'entity' => 'deal', 'field_key' => 'amount', 'field_type' => 'numeric', 'is_system' => true, 'label_en' => 'Amount', 'label_uk' => 'Сума', 'label_he' => 'סכום'],
            ['static_id' => 1005, 'entity' => 'counterparty', 'field_key' => 'manager_id', 'field_type' => 'user', 'is_system' => true, 'label_en' => 'Responsible', 'label_uk' => 'Відповідальний', 'label_he' => 'אחראי'],
            ['static_id' => 3001, 'entity' => 'product', 'field_key' => 'name', 'field_type' => 'text', 'is_system' => true, 'label_en' => 'Product Name', 'label_uk' => 'Назва товару', 'label_he' => 'שם המוצר'],
            ['static_id' => 3002, 'entity' => 'product', 'field_key' => 'sku', 'field_type' => 'text', 'is_system' => true, 'label_en' => 'SKU', 'label_uk' => 'Артикул (SKU)', 'label_he' => 'מק"ט'],
            ['static_id' => 3003, 'entity' => 'product', 'field_key' => 'price', 'field_type' => 'numeric', 'is_system' => true, 'label_en' => 'Price', 'label_uk' => 'Ціна', 'label_he' => 'מחיר'],
            ['static_id' => 3004, 'entity' => 'product', 'field_key' => 'description', 'field_type' => 'text', 'is_system' => true, 'label_en' => 'Description', 'label_uk' => 'Опис', 'label_he' => 'תיאור'],
            ['static_id' => 3005, 'entity' => 'product', 'field_key' => 'photo_url', 'field_type' => 'text', 'is_system' => true, 'label_en' => 'Photo URL', 'label_uk' => 'URL Фото', 'label_he' => 'כתובת תמונה'],
            ['static_id' => 2003, 'entity' => 'deal', 'field_key' => 'stage', 'field_type' => 'enum', 'is_system' => true, 'label_en' => 'Stage', 'label_uk' => 'Стадія', 'label_he' => 'שלב'],
            ['static_id' => 2004, 'entity' => 'deal', 'field_key' => 'counterparty_id', 'field_type' => 'relation', 'is_system' => true, 'label_en' => 'Counterparty', 'label_uk' => 'Контрагент', 'label_he' => 'לקוח'],
            ['static_id' => 2005, 'entity' => 'deal', 'field_key' => 'pipeline_id', 'field_type' => 'numeric', 'is_system' => true, 'label_en' => 'Pipeline', 'label_uk' => 'Воронка', 'label_he' => 'משפך מכירות'],
            ['static_id' => 2006, 'entity' => 'deal', 'field_key' => 'stage_id', 'field_type' => 'numeric', 'is_system' => true, 'label_en' => 'Stage', 'label_uk' => 'Стадія', 'label_he' => 'שלב'],
            ['static_id' => 10000, 'entity' => 'deal', 'field_key' => 'test', 'field_type' => 'text', 'is_system' => false, 'label_en' => 'test', 'label_uk' => 'тест', 'label_he' => 'test'],
            ['static_id' => 10001, 'entity' => 'counterparty', 'field_key' => 'test', 'field_type' => 'text', 'is_system' => false, 'label_en' => 'test', 'label_uk' => 'тест', 'label_he' => 'test'],
            ['static_id' => 10002, 'entity' => 'product', 'field_key' => 'test', 'field_type' => 'text', 'is_system' => false, 'label_en' => 'test', 'label_uk' => 'тест', 'label_he' => 'test'],
            ['static_id' => 10003, 'entity' => 'deal', 'field_key' => 'test2', 'field_type' => 'text', 'is_system' => false, 'label_en' => 'test2', 'label_uk' => 'тест2', 'label_he' => 'test2'],
            ['static_id' => 2007, 'entity' => 'deal', 'field_key' => 'responsible_id', 'field_type' => 'user', 'is_system' => true, 'label_en' => 'Responsible', 'label_uk' => 'Відповідальний', 'label_he' => 'אחראי'],
        ];

        foreach ($fields as $field) {
            DB::table('fields_metadata')->insert($field + ['created_at' => now(), 'updated_at' => now()]);
        }

        // 3. Вставляємо Пайплайни та Етапи
        $pipelines = [
            [
                'name' => 'B2C',
                'stages' => [
                    ['name' => 'Lead', 'color' => '#00abf5', 'sort_order' => 0, 'is_won' => false, 'is_lost' => false],
                    ['name' => 'Negotiation', 'color' => '#00ffaa', 'sort_order' => 1, 'is_won' => false, 'is_lost' => false],
                    ['name' => 'Closed Won', 'color' => '#00ff00', 'sort_order' => 2, 'is_won' => true, 'is_lost' => false],
                    ['name' => 'Closed Los', 'color' => '#ff0000', 'sort_order' => 3, 'is_won' => false, 'is_lost' => true],
                ]
            ],
            [
                'name' => 'test',
                'stages' => [
                    ['name' => 'Lead', 'color' => '#c8a45b', 'sort_order' => 0, 'is_won' => false, 'is_lost' => false],
                    ['name' => 'tes2', 'color' => '#3c87af', 'sort_order' => 1, 'is_won' => false, 'is_lost' => false],
                    ['name' => 'Closed Won', 'color' => '#00ff00', 'sort_order' => 2, 'is_won' => false, 'is_lost' => false],
                ]
            ]
        ];

        foreach ($pipelines as $p) {
            $pipelineId = DB::table('pipelines')->insertGetId([
                'name' => $p['name'],
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            foreach ($p['stages'] as $s) {
                DB::table('pipeline_stages')->insert($s + [
                    'pipeline_id' => $pipelineId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
}
