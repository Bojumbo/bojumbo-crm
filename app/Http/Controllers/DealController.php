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
        $deals = Deal::with(['fieldValues', 'products.fieldValues', 'products'])->latest()->get();
        $counterparties = Counterparty::with('fieldValues')->get();
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
            $deal->saveDynamicFields([2006 => $newStageId]);
            $deal->load('fieldValues', 'products');
            \App\Services\AutomationService::handleStageChange($deal, $newStageId);
        }
        return response()->json(['success' => true]);
    }
    public function store(Request $request)
    {
        $deal = Deal::create();
        ActivityLogService::logAction($deal, 'created');
        
        $deal->saveDynamicFields($request->input('fields', []));
        if ($request->has('products')) {
            $this->syncProducts($deal, $request->input('products', []));
        }
        
        return redirect()->back();
    }
    public function update(Request $request, Deal $deal)
    {
        $deal->saveDynamicFields($request->input('fields', []));
        if ($request->has('products')) {
            $this->syncProducts($deal, $request->input('products', []));
        }
        
        return redirect()->back();
    }
    public function fastUpdate(Request $request, Deal $deal)
    {
        $fieldId = (int)$request->input('field_id');
        $value = $request->input('value');
        
        $deal->saveDynamicFields([$fieldId => $value]);
        
        // Якщо змінено стадію (Static ID 2006), запускаємо автоматизації
        if ($fieldId === 2006) {
            \App\Services\AutomationService::handleStageChange($deal, (int)$value);
        }
        
        return response()->json(['success' => true]);
    }
    public function destroy(Deal $deal)
    {
        ActivityLogService::logAction($deal, 'deleted');
        $deal->delete();
        return redirect()->back();
    }
    protected function syncProducts(Deal $deal, array $products)
    {
        $syncData = [];
        $totalAmount = 0;
        
        foreach ($products as $p) {
            $productId = isset($p['id']) ? (int)$p['id'] : null;
            
            if ($productId) {
                // Враховуємо можливі різні імена полів qty/quantity
                $qty = (float)($p['qty'] ?? $p['quantity'] ?? 1);
                $price = (float)($p['price'] ?? 0);
                
                $syncData[$productId] = [
                    'quantity' => $qty,
                    'price_at_sale' => $price,
                ];
                
                $totalAmount += ($qty * $price);
            }
        }
        
        $deal->products()->sync($syncData);
        
        // Автоматично оновлюємо суму угоди (Static ID 2002)
        if ($totalAmount > 0) {
            $deal->saveDynamicFields([2002 => (string)$totalAmount]);
        }
    }
}