<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FieldsMetadataSeeder extends Seeder
{
    public function run(): void
    {
        $fields = [
            // --- COUNTERPARTIES (1000-1999) ---
            [
                'static_id' => 1001,
                'entity' => 'counterparty',
                'field_key' => 'name',
                'field_type' => 'text',
                'is_system' => true,
                'label_en' => 'Name/Company',
                'label_uk' => 'Назва/Ім\'я',
                'label_he' => 'שם/חברה',
            ],
            [
                'static_id' => 1002,
                'entity' => 'counterparty',
                'field_key' => 'email',
                'field_type' => 'text',
                'is_system' => true,
                'label_en' => 'Email',
                'label_uk' => 'Email',
                'label_he' => 'אימייל',
            ],
            [
                'static_id' => 1003,
                'entity' => 'counterparty',
                'field_key' => 'phone',
                'field_type' => 'text',
                'is_system' => true,
                'label_en' => 'Phone',
                'label_uk' => 'Телефон',
                'label_he' => 'טלפון',
            ],
            [
                'static_id' => 1004,
                'entity' => 'counterparty',
                'field_key' => 'type', // Individual or Company
                'field_type' => 'enum',
                'is_system' => true,
                'label_en' => 'Type',
                'label_uk' => 'Тип',
                'label_he' => 'סוג',
            ],
            [
                'static_id' => 1005,
                'entity' => 'counterparty',
                'field_key' => 'manager_id',
                'field_type' => 'user', // Тип - посилання на користувача
                'is_system' => true,
                'label_en' => 'Responsible',
                'label_uk' => 'Відповідальний',
                'label_he' => 'אחראי',
            ],

            // --- PRODUCTS (3000-3999) ---
            [
                'static_id' => 3001,
                'entity' => 'product',
                'field_key' => 'name',
                'field_type' => 'text',
                'is_system' => true,
                'label_en' => 'Product Name',
                'label_uk' => 'Назва товару',
                'label_he' => 'שם המוצר',
            ],
            [
                'static_id' => 3002,
                'entity' => 'product',
                'field_key' => 'sku',
                'field_type' => 'text',
                'is_system' => true,
                'label_en' => 'SKU',
                'label_uk' => 'Артикул (SKU)',
                'label_he' => 'מק"ט',
            ],
            [
                'static_id' => 3003,
                'entity' => 'product',
                'field_key' => 'price',
                'field_type' => 'numeric',
                'is_system' => true,
                'label_en' => 'Price',
                'label_uk' => 'Ціна',
                'label_he' => 'מחיר',
            ],
            [
                'static_id' => 3004,
                'entity' => 'product',
                'field_key' => 'description',
                'field_type' => 'text',
                'is_system' => true,
                'label_en' => 'Description',
                'label_uk' => 'Опис',
                'label_he' => 'תיאור',
            ],
            [
                'static_id' => 3005,
                'entity' => 'product',
                'field_key' => 'photo_url',
                'field_type' => 'text', // Поки що як текст/URL
                'is_system' => true,
                'label_en' => 'Photo URL',
                'label_uk' => 'URL Фото',
                'label_he' => 'כתובת תמונה',
            ],

            // --- DEALS (2000-2999) ---
            [
                'static_id' => 2001,
                'entity' => 'deal',
                'field_key' => 'title',
                'field_type' => 'text',
                'is_system' => true,
                'label_en' => 'Deal Title',
                'label_uk' => 'Назва угоди',
                'label_he' => 'שם העסקה',
            ],
            [
                'static_id' => 2002,
                'entity' => 'deal',
                'field_key' => 'amount',
                'field_type' => 'numeric',
                'is_system' => true,
                'label_en' => 'Amount',
                'label_uk' => 'Сума',
                'label_he' => 'סכום',
            ],
            [
                'static_id' => 2003,
                'entity' => 'deal',
                'field_key' => 'stage',
                'field_type' => 'enum', // Стадія воронки
                'is_system' => true,
                'label_en' => 'Stage',
                'label_uk' => 'Стадія',
                'label_he' => 'שלב',
            ],
            [
                'static_id' => 2004,
                'entity' => 'deal',
                'field_key' => 'counterparty_id',
                'field_type' => 'relation', // Зв'язок з контрагентом
                'is_system' => true,
                'label_en' => 'Counterparty',
                'label_uk' => 'Контрагент',
                'label_he' => 'לקוח',
            ],
            [
                'static_id' => 2005,
                'entity' => 'deal',
                'field_key' => 'pipeline_id',
                'field_type' => 'numeric',
                'is_system' => true,
                'label_en' => 'Pipeline',
                'label_uk' => 'Воронка',
                'label_he' => 'משפך מכירות',
            ],
            [
                'static_id' => 2006,
                'entity' => 'deal',
                'field_key' => 'stage_id',
                'field_type' => 'numeric',
                'is_system' => true,
                'label_en' => 'Stage',
                'label_uk' => 'Стадія',
                'label_he' => 'שלב',
            ],
            [
                'static_id' => 2007,
                'entity' => 'deal',
                'field_key' => 'responsible_id',
                'field_type' => 'user', 
                'is_system' => true,
                'label_en' => 'Responsible',
                'label_uk' => 'Відповідальний',
                'label_he' => 'אחראי',
            ],
        ];

        foreach ($fields as $field) {
            DB::table('fields_metadata')->updateOrInsert(
            ['static_id' => $field['static_id']],
                array_merge($field, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
