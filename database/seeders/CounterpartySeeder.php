<?php

namespace Database\Seeders;

use App\Models\Counterparty;
use App\Models\CounterpartyFieldValue;
use Illuminate\Database\Seeder;

class CounterpartySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Створюємо фіз. особу
        $person = Counterparty::create(['type' => 'individual']);

        $fields = [
            1001 => 'Oleksandr Boiko', // Name
            1002 => 'boyko@example.com', // Email
            1003 => '+380 67 000 00 01', // Phone
            1004 => 'individual', // Type
        ];

        foreach ($fields as $id => $val) {
            CounterpartyFieldValue::create([
                'counterparty_id' => $person->id,
                'static_id' => $id,
                'value' => $val
            ]);
        }

        // 2. Створюємо компанію
        $company = Counterparty::create(['type' => 'company']);

        $cFields = [
            1001 => 'Bojumbo Soft',
            1002 => 'office@bojumbo.test',
            1003 => '+380 44 123 45 67',
            1004 => 'company',
        ];

        foreach ($cFields as $id => $val) {
            CounterpartyFieldValue::create([
                'counterparty_id' => $company->id,
                'static_id' => $id,
                'value' => $val
            ]);
        }
    }
}
