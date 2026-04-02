<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\Counterparty;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        // Початкова та кінцева дати (за замовчуванням - поточний місяць)
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $PIPELINE_FIELD = 2005;
        $STAGE_FIELD = 2006;
        $MANAGER_FIELD = 2007;
        $AMOUNT_FIELD = 2002;

        // Базовий запит для угод у вибраному періоді
        // Додаємо перевірку, чи належить угода до якоїсь воронки, щоб уникнути "фантомних" записів
        $dealsQuery = Deal::whereBetween('deals.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->whereExists(function ($query) use ($PIPELINE_FIELD) {
                $query->select(DB::raw(1))
                      ->from('deal_field_values')
                      ->whereColumn('deal_field_values.deal_id', 'deals.id')
                      ->where('deal_field_values.static_id', $PIPELINE_FIELD);
            });
            
        $dealIds = (clone $dealsQuery)->pluck('id');

        // 1. Загальні метрики
        $totalDeals = $dealsQuery->count();
        $totalAmount = DB::table('deal_field_values')
            ->whereIn('deal_id', $dealIds)
            ->where('static_id', $AMOUNT_FIELD)
            ->selectRaw('SUM(CAST(NULLIF(value, \'\') AS NUMERIC)) as aggregate')
            ->value('aggregate') ?? 0;

        // Всі стадії для вибору Won/Lost
        $stages = PipelineStage::all();
        $wonStageIds = $stages->where('is_won', true)->pluck('id')->toArray();
        $lostStageIds = $stages->where('is_lost', true)->pluck('id')->toArray();

        // 2. Статистика по менеджерах
        $managerStats = User::all()->map(function($user) use ($dealIds, $MANAGER_FIELD, $STAGE_FIELD, $wonStageIds, $lostStageIds) {
            $userDealIds = DB::table('deal_field_values')
                ->whereIn('deal_id', $dealIds)
                ->where('static_id', $MANAGER_FIELD)
                ->where('value', $user->id)
                ->pluck('deal_id');

            $wonCount = DB::table('deal_field_values')
                ->whereIn('deal_id', $userDealIds)
                ->where('static_id', $STAGE_FIELD)
                ->whereIn('value', $wonStageIds)
                ->count();

            $lostCount = DB::table('deal_field_values')
                ->whereIn('deal_id', $userDealIds)
                ->where('static_id', $STAGE_FIELD)
                ->whereIn('value', $lostStageIds)
                ->count();

            $total = $userDealIds->count();

            return [
                'id' => $user->id,
                'name' => $user->name,
                'total' => $total,
                'won' => $wonCount,
                'lost' => $lostCount,
                'in_work' => $total - $wonCount - $lostCount,
            ];
        });

        // 3. Топ клієнтів (за замовчуванням за кількістю угод)
        $COUNTERPARTY_FIELD = 2004;
        $topClientIds = DB::table('deal_field_values')
            ->whereIn('deal_id', $dealIds)
            ->where('static_id', $COUNTERPARTY_FIELD)
            ->select('value', DB::raw('count(*) as count'))
            ->groupBy('value')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        $clientStats = $topClientIds->map(function($item) use ($dealIds, $COUNTERPARTY_FIELD, $STAGE_FIELD, $AMOUNT_FIELD, $wonStageIds, $lostStageIds) {
            $client = Counterparty::find($item->value);
            if (!$client) return null;

            $clientDealIds = DB::table('deal_field_values')
                ->whereIn('deal_id', $dealIds)
                ->where('static_id', $COUNTERPARTY_FIELD)
                ->where('value', $client->id)
                ->pluck('deal_id');

            $wonCount = DB::table('deal_field_values')
                ->whereIn('deal_id', $clientDealIds)
                ->where('static_id', $STAGE_FIELD)
                ->whereIn('value', $wonStageIds)
                ->count();

            $lostCount = DB::table('deal_field_values')
                ->whereIn('deal_id', $clientDealIds)
                ->where('static_id', $STAGE_FIELD)
                ->whereIn('value', $lostStageIds)
                ->count();

            $amount = DB::table('deal_field_values')
                ->whereIn('deal_id', $clientDealIds)
                ->where('static_id', $AMOUNT_FIELD)
                ->selectRaw('SUM(CAST(NULLIF(value, \'\') AS NUMERIC)) as aggregate')
                ->value('aggregate') ?? 0;

            $name = $client->fieldValues->where('static_id', 1001)->first()?->value ?? 'No Name';

            return [
                'name' => $name,
                'total' => $clientDealIds->count(),
                'won' => $wonCount,
                'lost' => $lostCount,
                'in_work' => $clientDealIds->count() - $wonCount - $lostCount,
                'amount' => $amount
            ];
        })->filter();

        // 4. Детальна статистика по воронках
        $performanceData = Pipeline::with('stages')->get()->map(function($pipeline) use ($dealIds, $STAGE_FIELD, $PIPELINE_FIELD, $wonStageIds, $lostStageIds) {
            $pwonIds = $pipeline->stages->where('is_won', true)->pluck('id')->toArray();
            $plostIds = $pipeline->stages->where('is_lost', true)->pluck('id')->toArray();
            
            $pipelineDealIds = DB::table('deal_field_values')
                ->whereIn('deal_id', $dealIds)
                ->where('static_id', $PIPELINE_FIELD)
                ->where('value', $pipeline->id)
                ->pluck('deal_id');

            $wonCount = DB::table('deal_field_values')
                ->whereIn('deal_id', $pipelineDealIds)
                ->where('static_id', $STAGE_FIELD)
                ->whereIn('value', $pwonIds)
                ->count();

            $lostCount = DB::table('deal_field_values')
                ->whereIn('deal_id', $pipelineDealIds)
                ->where('static_id', $STAGE_FIELD)
                ->whereIn('value', $plostIds)
                ->count();

            $total = $pipelineDealIds->count();

            return [
                'id' => $pipeline->id,
                'name' => $pipeline->name,
                'total' => $total,
                'won' => $wonCount,
                'lost' => $lostCount,
                'in_work' => $total - $wonCount - $lostCount,
                'stages' => $pipeline->stages->map(function($stage) use ($pipelineDealIds, $STAGE_FIELD) {
                    return [
                        'name' => $stage->name,
                        'count' => DB::table('deal_field_values')
                            ->whereIn('deal_id', $pipelineDealIds)
                            ->where('static_id', $STAGE_FIELD)
                            ->where('value', $stage->id)
                            ->count()
                    ];
                })
            ];
        });

        return view('statistics.index', compact(
            'totalDeals', 'totalAmount', 'managerStats', 'clientStats', 'performanceData', 'startDate', 'endDate'
        ));
    }
}
