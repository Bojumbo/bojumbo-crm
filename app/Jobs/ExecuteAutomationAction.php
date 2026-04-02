<?php

namespace App\Jobs;

use App\Models\Automation;
use App\Models\Deal;
use App\Services\ActivityLogService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExecuteAutomationAction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected Automation $automation,
        protected Deal $deal
    ) {}

    public function handle()
    {
        Log::info("Executing Background Automation #{$this->automation->id} on Deal #{$this->deal->id}");

        switch ($this->automation->action_type) {
            case 'duplicate_deal':
                $this->duplicateDeal($this->deal, $this->automation->action_payload ?: []);
                break;
            case 'send_webhook':
                $this->sendWebhook($this->deal, $this->automation->action_payload ?: []);
                break;
        }
    }

    protected function duplicateDeal(Deal $originalDeal, array $payload)
    {
        $targetPipelineId = $payload['target_pipeline_id'] ?? null;
        $targetStageId = $payload['target_stage_id'] ?? null;
        $targetUserId = $payload['target_user_id'] ?? null;

        if (!$targetPipelineId || !$targetStageId) return;

        // Eager load for duplication performance
        $originalDeal->load('fieldValues', 'products');

        $newDeal = Deal::create();
        ActivityLogService::logAction($newDeal, 'created_via_automation');

        $fields = [];
        foreach ($originalDeal->fieldValues as $fv) {
            // Skip stage/pipeline fields as they are target-specific
            if (in_array($fv->static_id, [2005, 2006])) continue;
            $fields[$fv->static_id] = $fv->value;
        }

        $fields[2005] = $targetPipelineId;
        $fields[2006] = $targetStageId;
        if ($targetUserId) $fields[2007] = $targetUserId;

        $newDeal->saveDynamicFields($fields);

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

        // Trigger automations for the new deal (recursive dispatch)
        \App\Services\AutomationService::handleStageChange($newDeal, $targetStageId);
    }

    protected function sendWebhook(Deal $deal, array $payload)
    {
        $url = $payload['url'] ?? null;
        if (!$url) return;

        $deal->load('fieldValues');

        $data = [
            'id' => $deal->id,
            'title' => $deal->getFieldValue(2001),
            'amount' => $deal->getFieldValue(2002),
            'pipeline_id' => $deal->getFieldValue(2005),
            'stage_id' => $deal->getFieldValue(2006),
            'responsible_id' => $deal->getFieldValue(2007),
            'created_at' => $deal->created_at,
            'fields' => $deal->fieldValues->pluck('value', 'static_id')->toArray()
        ];

        try {
            Http::timeout(10)->post($url, $data);
            Log::info("Webhook sent to {$url} for deal #{$deal->id}");
        } catch (\Exception $e) {
            Log::error("Webhook failed to {$url}: " . $e->getMessage());
        }
    }
}
