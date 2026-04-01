<?php

namespace App\Http\Controllers;

use App\Models\Automation;
use Illuminate\Http\Request;

class AutomationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'pipeline_id' => 'required|exists:pipelines,id',
            'pipeline_stage_id' => 'required|exists:pipeline_stages,id',
            'action_type' => 'required|string|in:duplicate_deal,send_webhook',
            'action_payload' => 'required|array',
            'action_payload.target_pipeline_id' => 'required_if:action_type,duplicate_deal|nullable|exists:pipelines,id',
            'action_payload.target_stage_id' => 'required_if:action_type,duplicate_deal|nullable|exists:pipeline_stages,id',
            'action_payload.url' => 'required_if:action_type,send_webhook|url',
        ]);

        $automation = Automation::create([
            'pipeline_id' => $request->pipeline_id,
            'pipeline_stage_id' => $request->pipeline_stage_id,
            'action_type' => $request->action_type,
            'action_payload' => $request->action_payload,
            'is_active' => true,
        ]);

        return response()->json(['success' => true, 'automation' => $automation]);
    }

    public function update(Request $request, Automation $automation)
    {
        $request->validate([
            'pipeline_id' => 'required|exists:pipelines,id',
            'pipeline_stage_id' => 'required|exists:pipeline_stages,id',
            'action_type' => 'required|string|in:duplicate_deal,send_webhook',
            'action_payload' => 'required|array',
            'action_payload.target_pipeline_id' => 'required_if:action_type,duplicate_deal|nullable|exists:pipelines,id',
            'action_payload.target_stage_id' => 'required_if:action_type,duplicate_deal|nullable|exists:pipeline_stages,id',
            'action_payload.url' => 'required_if:action_type,send_webhook|url',
        ]);

        $automation->update([
            'pipeline_id' => $request->pipeline_id,
            'pipeline_stage_id' => $request->pipeline_stage_id,
            'action_type' => $request->action_type,
            'action_payload' => $request->action_payload,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return response()->json(['success' => true, 'automation' => $automation]);
    }

    public function destroy(Automation $automation)
    {
        $automation->delete();
        return response()->json(['success' => true]);
    }
}
