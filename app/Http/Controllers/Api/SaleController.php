<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GreenhouseResource;
use App\Http\Resources\SaleResource;
use App\Models\Greenhouse;
use App\Models\MelonVariety;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $greenhouses = $user->isAdmin()
            ? Greenhouse::orderBy('code')->get()
            : $user->greenhouses()->orderBy('code')->get();

        $greenhouses->loadCount(['sales']);

        $salesSummary = Sale::query()
            ->whereIn('greenhouse_id', $greenhouses->pluck('id'))
            ->selectRaw('
                greenhouse_id,
                COALESCE(SUM(CASE WHEN DATE(created_at) = ? THEN total ELSE 0 END), 0) as today_total,
                COALESCE(SUM(CASE WHEN YEAR(created_at) = ? AND MONTH(created_at) = ? THEN total ELSE 0 END), 0) as month_total
            ', [now()->toDateString(), now()->year, now()->month])
            ->groupBy('greenhouse_id')
            ->get()
            ->keyBy('greenhouse_id');

        $data = $greenhouses->map(fn ($gh) => [
            'greenhouse'   => new GreenhouseResource($gh),
            'sales_count'  => $gh->sales_count,
            'today_total'  => (float) ($salesSummary[$gh->id]->today_total ?? 0),
            'month_total'  => (float) ($salesSummary[$gh->id]->month_total ?? 0),
        ]);

        return response()->json(['data' => $data]);
    }

    public function greenhouse(Request $request, Greenhouse $greenhouse): JsonResponse
    {
        $this->abortIfCannotAccessSale($request, $greenhouse->id);

        $todayRevenue = Sale::where('greenhouse_id', $greenhouse->id)
            ->whereDate('created_at', now()->toDateString())
            ->sum('total');

        $monthRevenue = Sale::where('greenhouse_id', $greenhouse->id)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total');

        $totalRevenue     = Sale::where('greenhouse_id', $greenhouse->id)->sum('total');
        $transactionCount = Sale::where('greenhouse_id', $greenhouse->id)->count();

        $recentSales = Sale::with(['user', 'items.melonVariety'])
            ->where('greenhouse_id', $greenhouse->id)
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'greenhouse'       => new GreenhouseResource($greenhouse),
            'today_revenue'    => (float) $todayRevenue,
            'month_revenue'    => (float) $monthRevenue,
            'total_revenue'    => (float) $totalRevenue,
            'transaction_count' => $transactionCount,
            'recent_sales'     => SaleResource::collection($recentSales),
        ]);
    }

    public function paginatedSales(Request $request, Greenhouse $greenhouse): JsonResponse
    {
        $this->abortIfCannotAccessSale($request, $greenhouse->id);

        $sales = Sale::with(['user', 'items.melonVariety'])
            ->where('greenhouse_id', $greenhouse->id)
            ->latest()
            ->paginate(15);

        return SaleResource::collection($sales)->response();
    }

    public function store(Request $request, Greenhouse $greenhouse): JsonResponse
    {
        $this->abortIfCannotAccessSale($request, $greenhouse->id);

        $validated = $request->validate([
            'buyer_name'                          => 'required|string|max:255',
            'sale_items'                          => 'required|array|min:1',
            'sale_items.*.melon_variety_id'       => 'required|exists:melon_varieties,id',
            'sale_items.*.weight_kg'              => 'required|numeric|min:0.001',
            'sale_items.*.price_per_kg'           => 'required|numeric|min:0',
        ]);

        $total = 0;
        $items = [];
        foreach ($validated['sale_items'] as $item) {
            $subtotal = $item['weight_kg'] * $item['price_per_kg'];
            $total   += $subtotal;
            $items[]  = [
                'melon_variety_id' => $item['melon_variety_id'],
                'weight_kg'        => $item['weight_kg'],
                'price_per_kg'     => $item['price_per_kg'],
                'subtotal'         => $subtotal,
            ];
        }

        $sale = Sale::create([
            'greenhouse_id' => $greenhouse->id,
            'user_id'       => $request->user()->id,
            'buyer_name'    => $validated['buyer_name'],
            'total'         => $total,
        ]);

        foreach ($items as $item) {
            $sale->items()->create($item);
        }

        $sale->load(['user', 'items.melonVariety']);

        return response()->json(new SaleResource($sale), 201);
    }

    public function show(Request $request, Sale $sale): JsonResponse
    {
        $sale->load(['greenhouse', 'user', 'items.melonVariety']);
        $this->abortIfCannotAccessSale($request, $sale->greenhouse_id);

        return response()->json(new SaleResource($sale));
    }

    public function destroy(Request $request, Sale $sale): JsonResponse
    {
        $sale->load('greenhouse');
        $this->abortIfCannotAccessSale($request, $sale->greenhouse_id);

        $sale->delete();

        return response()->json(['message' => 'Penjualan berhasil dihapus.']);
    }

    public function report(Request $request, Greenhouse $greenhouse): JsonResponse
    {
        $this->abortIfCannotAccessSale($request, $greenhouse->id);

        $from = $request->query('from');
        $to   = $request->query('to');

        $report = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('melon_varieties', 'sale_items.melon_variety_id', '=', 'melon_varieties.id')
            ->where('sales.greenhouse_id', $greenhouse->id)
            ->when($from, fn ($q) => $q->whereDate('sales.created_at', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('sales.created_at', '<=', $to))
            ->selectRaw('
                melon_varieties.name as variety_name,
                COUNT(sale_items.id) as total_transaksi,
                COALESCE(SUM(sale_items.weight_kg), 0) as total_berat,
                COALESCE(SUM(sale_items.subtotal), 0) as total_omset
            ')
            ->groupBy('melon_varieties.id', 'melon_varieties.name')
            ->orderByDesc('total_berat')
            ->get();

        return response()->json([
            'greenhouse'   => new GreenhouseResource($greenhouse),
            'filters'      => ['from' => $from, 'to' => $to],
            'report'       => $report,
            'grand_weight' => $report->sum('total_berat'),
            'grand_omset'  => $report->sum('total_omset'),
        ]);
    }

    public function varieties(): JsonResponse
    {
        return response()->json(MelonVariety::orderBy('name')->get(['id', 'name', 'slug']));
    }

    private function abortIfCannotAccessSale(Request $request, ?int $greenhouseId): void
    {
        if ($request->user()->isAdmin()) {
            return;
        }

        if (! $greenhouseId || ! $request->user()->greenhouses()->where('id', $greenhouseId)->exists()) {
            abort(403, 'Anda tidak memiliki akses ke greenhouse ini.');
        }
    }
}
