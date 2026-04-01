<?php

namespace App\Services;

use App\Models\Automation;
use App\Models\Deal;

class AutomationService
{
    /**
     * Prevent infinite recursive loops.
     */
    protected static $depth = 0;

    public static function handleStageChange(Deal $deal, $newStageId)
    {
        if (self::$depth > 5)
            return;
        self::$depth++;

        try {
            \Illuminate\Support\Facades\Log::info("handleStageChange START. Deal ID: {$deal->id}. New Stage: {$newStageId}. Depth: " . self::$depth);
            $automations = Automation::where('pipeline_stage_id', $newStageId)
                ->where('is_active', true)
                ->get();
            \Illuminate\Support\Facades\Log::info("Found " . $automations->count() . " active automations for stage {$newStageId}");

            foreach ($automations as $automation) {
                \Illuminate\Support\Facades\Log::info("Executing automation {$automation->id} with action: {$automation->action_type}");
                switch ($automation->action_type) {
                    case 'duplicate_deal':
                        self::duplicateDeal($deal, $automation->action_payload ?: []);
                        session()->flash('automation_success', __('Автоматизація виконана: створено дублікат угоди.'));
                        break;
                    case 'send_webhook':
                        self::sendWebhook($deal, $automation->action_payload ?: []);
                        break;
                }
            }

        }
        catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Automation error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }

        self::$depth--;
    }

    protected static function duplicateDeal(Deal $originalDeal, array $payload)
    {
        $targetPipelineId = $payload['target_pipeline_id'] ?? null;
        $targetStageId = $payload['target_stage_id'] ?? null;
        $targetUserId = $payload['target_user_id'] ?? null;

        \Illuminate\Support\Facades\Log::info("duplicateDeal payload: ", $payload);

        if (!$targetPipelineId || !$targetStageId) {
            \Illuminate\Support\Facades\Log::info("duplicateDeal aborted: Missing target pipeline or stage.");
            return;
        }

        $newDeal = Deal::create();
        \Illuminate\Support\Facades\Log::info("Created duplicate Deal ID: {$newDeal->id}");

        $fields = [];
        foreach ($originalDeal->fieldValues as $fv) {
            if ($fv->static_id == 2005 || $fv->static_id == 2006) {
                continue;
            }
            $fields[$fv->static_id] = $fv->value;
        }

        $fields[2005] = $targetPipelineId;
        $fields[2006] = $targetStageId;
        
        if ($targetUserId) {
            $fields[2007] = $targetUserId;
        }

        foreach ($fields as $staticId => $val) {
            $newDeal->fieldValues()->updateOrCreate(
            ['static_id' => $staticId],
            ['value' => is_array($val) ? json_encode($val) : $val]
            );
        }

        $syncData = [];
        foreach ($originalDeal->products as $p) {
            $syncData[$p->id] = [
                'quantity' => $p->pivot->quantity,
                'price_at_sale' => $p->pivot->price_at_sale,
            ];
        }
        if (!empty($syncData)) {
            $newDeal->products()->sync($syncData);
        }

        // Trigger automations for the newly created duplicate in its target stage
        self::handleStageChange($newDeal, $targetStageId);
    }

    protected static function sendWebhook(Deal $deal, array $payload)
    {
        $url = $payload['url'] ?? null;
        if (!$url) return;

        // Збираємо дані угоди
        $data = [
            'id' => $deal->id,
            'title' => $deal->getFieldValue(2001),
            'amount' => $deal->getFieldValue(2002),
            'pipeline_id' => $deal->getFieldValue(2005),
            'stage_id' => $deal->getFieldValue(2006),
            'responsible_id' => $deal->getFieldValue(2007),
            'created_at' => $deal->created_at,
            'fields' => []
        ];

        // Додаємо всі значення полів
        foreach ($deal->fieldValues as $fv) {
            $data['fields'][$fv->field_static_id] = $fv->value;
        }

        try {
            \Illuminate\Support\Facades\Http::timeout(5)->post($url, $data);
            \Illuminate\Support\Facades\Log::info("Webhook sent to {$url} for deal #{$deal->id}");
            
            if (session()->isStarted()) {
                session()->flash('automation_message', "Webhook sent to {$url}");
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Webhook failed to {$url}: " . $e->getMessage());
        }
    }
}
