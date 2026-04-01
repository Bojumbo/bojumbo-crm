<?php namespace App\Http\Controllers;
use App\Models\ActivityLog;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query("type");
        $id = $request->query("id");
        if (!$type || !$id) {
            return response()->json([]);
        }
        $logs = ActivityLog::with(["user"])
            ->where("loggable_type", $type)
            ->where("loggable_id", $id)
            ->where("action", "comment")
            ->latest()
            ->get()
            ->map(function ($log) {
                return [
                    "id" => $log->id,
                    "user" => $log->user?->name ?? "System",
                    "action" => $log->action,
                    "new_value" => $log->new_value,
                    "created_at" => $log->created_at->diffForHumans()
                ];
            });
        return response()->json($logs);
    }
    public function store(Request $request)
    {
        Log::info('ActivityController@store submission', $request->all());
        try {
            $request->validate([
                "loggable_type" => "required|string",
                "loggable_id" => "required|integer",
                "comment" => "required|string"
            ]);
            $modelClass = $request->loggable_type;
            
            if (!class_exists($modelClass)) {
                Log::error("Model class not found: {$modelClass}");
                return response()->json(['error' => "Model class {$modelClass} not found"], 422);
            }
            $model = $modelClass::findOrFail($request->loggable_id);
            ActivityLogService::logComment($model, $request->comment);
            return response()->json(["success" => true]);
        } catch (\Exception $e) {
            Log::error('ActivityController@store error: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function ping()
    {
        return response()->json(['status' => 'ok', 'message' => 'ActivityController is alive']);
    }
}