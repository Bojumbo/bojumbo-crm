<?php

namespace App\Http\Controllers;

use App\Models\Pipeline;
use App\Models\PipelineStage;
use Illuminate\Http\Request;

class PipelineController extends Controller
{
    public function index()
    {
        $pipelines = Pipeline::with('stages')->get();
        return view('admin.pipelines.index', compact('pipelines'));
    }

    public function store(Request $request)
    {
        $pipeline = Pipeline::create(['name' => $request->name]);

        if ($request->has('stages')) {
            foreach ($request->stages as $index => $stageName) {
                if ($stageName) {
                    $pipeline->stages()->create([
                        'name' => $stageName,
                        'sort_order' => $index,
                        'color' => $request->colors[$index] ?? '#37352f',
                        'is_won' => isset($request->is_won[$index]) && $request->is_won[$index] == 1,
                        'is_lost' => isset($request->is_lost[$index]) && $request->is_lost[$index] == 1,
                    ]);
                }
            }
        }

        return redirect()->route('admin.pipelines.index');
    }

    public function update(Request $request, Pipeline $pipeline)
    {
        $pipeline->update(['name' => $request->name]);

        $stageIdsToKeep = collect($request->stage_ids)->filter()->toArray();

        // Не видаляємо етапи, якщо вони були видалені в інтерфейсі (SoftDeletes handles it conceptually, but here we hard delete orphaned stages)
        $pipeline->stages()->whereNotIn('id', $stageIdsToKeep)->delete();

        if ($request->has('stages')) {
            foreach ($request->stages as $index => $stageName) {
                if ($stageName) {
                    $stageId = $request->stage_ids[$index] ?? null;
                    $data = [
                        'name' => $stageName,
                        'sort_order' => $index,
                        'color' => $request->colors[$index] ?? '#37352f',
                        'is_won' => isset($request->is_won[$index]) && $request->is_won[$index] == 1,
                        'is_lost' => isset($request->is_lost[$index]) && $request->is_lost[$index] == 1,
                    ];

                    if ($stageId) {
                        $pipeline->stages()->where('id', $stageId)->update($data);
                    }
                    else {
                        $pipeline->stages()->create($data);
                    }
                }
            }
        }

        return redirect()->route('admin.pipelines.index');
    }

    public function destroy(Pipeline $pipeline)
    {
        $pipeline->delete();
        return redirect()->route('admin.pipelines.index');
    }
}
