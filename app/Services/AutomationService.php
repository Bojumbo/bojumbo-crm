<?php

namespace App\Services;

use App\Models\Automation;
use App\Models\Deal;
use Illuminate\Support\Facades\Log;

class AutomationService
{
    /**
     * Prevent infinite recursive loops.
     */
    protected static $depth = 0;

    public static function handleStageChange(Deal $deal, $newStageId)
    {
        if (self::$depth > 5) return;
        self::$depth++;

        try {
            Log::info("handleStageChange triggered. Deal ID: {$deal->id}. New Stage: {$newStageId}");
            
            $automations = Automation::where('pipeline_stage_id', $newStageId)
                ->where('is_active', true)
                ->get();

            foreach ($automations as $automation) {
                \App\Jobs\ExecuteAutomationAction::dispatch($automation, $deal);
            }
        } catch (\Exception $e) {
            Log::error("Automation dispatch error: " . $e->getMessage());
        }

        self::$depth--;
    }
}
