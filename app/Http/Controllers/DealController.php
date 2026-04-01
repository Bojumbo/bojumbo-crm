<?php
namespace App\Http\Controllers;
use App\Models\Deal;
use App\Models\FieldMetadata;
use App\Models\Counterparty;
use App\Models\Pipeline;
use App\Models\Product;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
class DealController extends Controller
{
    public function index(Request $request)
    {
        $pipelines = Pipeline::with('stages')->get();
        
        // Вибираємо активну воронку (першу за замовчуванням або з URL)
        $currentPipelineId = $request->query('pipeline_id', $pipelines->first()?->id);
        $currentPipeline = $pipelines->find($currentPipelineId);
        // Вибираємо ВСІ поля для угод, включаючи кастомні (ID 10000+)
        $columns = FieldMetadata::where('entity', 'deal')
            ->orderBy('static_id')
            ->get();
        $deals = Deal::with(['fieldValues', 'products'])->latest()->get();
        $counterparties = Counterparty::all();
        $allProducts = Product::with('fieldValues')->get();
        $users = User::all();
        return view('deals.index', compact('deals', 'columns', 'counterparties', 'pipelines', 'currentPipeline', 'allProducts', 'users'));
    }
    /**
     * Оновлення стадії при перетягуванні в Канбані
     */
    public function move(Request $request, Deal $deal)
    {
        $newStageId = (int) $request->input('stage_id');
        \Illuminate\Support\Facades\Log::info("Deal move triggered. Deal ID: {$deal->id}, New Stage ID: {$newStageId}");
        
        if ($newStageId) {
            $this->saveFields($deal, [2006 => $newStageId]);
            $deal->load('fieldValues', 'products');
            \App\Services\AutomationService::handleStageChange($deal, $newStageId);
        }
        return response()->json(['success' => true]);
    }
    public function store(Request $request)
    {
        $deal = Deal::create();
        ActivityLogService::logAction($deal, 'created');
        
        $this->saveFields($deal, $request->input('fields', []));
        if ($request->has('products')) {
            $this->syncProducts($deal, $request->input('products', []));
        }
        
        return redirect()->back();
    }
    public function update(Request $request, Deal $deal)
    {
        $this->saveFields($deal, $request->input('fields', []));
        if ($request->has('products')) {
            $this->syncProducts($deal, $request->input('products', []));
        }
        
        return redirect()->back();
    }
    public function destroy(Deal $deal)
    {
        ActivityLogService::logAction($deal, 'deleted');
        $deal->delete();
        return redirect()->back();
    }
    protected function saveFields(Deal $deal, array $fields)
    {
        foreach ($fields as $staticId => $newValue) {
            $oldFieldValue = $deal->fieldValues()->where('static_id', $staticId)->first();
            $oldValue = $oldFieldValue ? $oldFieldValue->value : null;
            if ($newValue !== null && $newValue != $oldValue) {
                // Логуємо зміну поля
                ActivityLogService::logFieldChange($deal, (int)$staticId, $oldValue, $newValue);
                
                $deal->fieldValues()->updateOrCreate(
                    ['static_id' => $staticId],
                    ['value' => is_array($newValue) ? json_encode($newValue) : $newValue]
                );
            }
        }
    }
    protected function syncProducts(Deal $deal, array $products)
    {
        $syncData = [];
        $totalAmount = 0;
        foreach ($products as $p) {
            if (!empty($p['id'])) {
                $syncData[$p['id']] = [
                    'quantity' => $p['qty'] ?? 1,
                    'price_at_sale' => $p['price'] ?? 0,
                ];
                $totalAmount += ($p['qty'] ?? 1) * ($p['price'] ?? 0);
            }
        }
        $deal->products()->sync($syncData);
        // Автоматично оновлюємо суму угоди (Static ID 2002)
        if ($totalAmount > 0) {
            $this->saveFields($deal, [2002 => $totalAmount]);
        }
    }
}