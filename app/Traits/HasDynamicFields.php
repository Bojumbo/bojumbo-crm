<?php

namespace App\Traits;

use App\Services\ActivityLogService;

trait HasDynamicFields
{
    /**
     * Оптимізоване збереження динамічних полів.
     * Завантажує існуючі значення одним запитом і оновлює лише змінені.
     */
    public function saveDynamicFields(array $fields)
    {
        // Завантажуємо існуючі поля, якщо вони ще не завантажені
        $this->loadMissing('fieldValues');
        $existingFields = $this->fieldValues->keyBy('static_id');

        foreach ($fields as $staticId => $newValue) {
            $staticId = (int) $staticId;
            $oldFieldValue = $existingFields->get($staticId);
            $oldValue = $oldFieldValue ? $oldFieldValue->value : null;

            // Приводимо масиви до JSON для порівняння
            $finalValue = is_array($newValue) ? json_encode($newValue) : $newValue;

            if ($newValue !== null && (string)$finalValue !== (string)$oldValue) {
                // Логуємо зміну поля, якщо це не нове створення (id вже існує)
                if ($this->exists) {
                    ActivityLogService::logFieldChange($this, $staticId, $oldValue, $finalValue);
                }

                $this->fieldValues()->updateOrCreate(
                    ['static_id' => $staticId],
                    ['value' => $finalValue]
                );
            }
        }
    }

    /**
     * Отримати значення поля за його Static ID
     */
    public function getFieldValue(int $staticId)
    {
        // Використовуємо завантажені відносини для швидкості (якщо колекція вже є)
        return $this->fieldValues->where('static_id', $staticId)->first()?->value;
    }
}
