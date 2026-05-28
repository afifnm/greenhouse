<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Greenhouse;
use App\Models\MelonVariety;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function report(Request $request, Greenhouse $greenhouse)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        $query = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('melon_varieties', 'sale_items.melon_variety_id', '=', 'melon_varieties.id')
            ->where('sales.greenhouse_id', $greenhouse->id)
            ->when($from, fn($q) => $q->whereDate('sales.created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('sales.created_at', '<=', $to));

        $report = (clone $query)
            ->selectRaw('
                melon_varieties.name as variety_name,
                COUNT(sale_items.id) as total_transaksi,
                COALESCE(SUM(sale_items.weight_kg), 0) as total_berat,
                COALESCE(SUM(sale_items.subtotal), 0) as total_omset
            ')
            ->groupBy('melon_varieties.id', 'melon_varieties.name')
            ->orderByDesc('total_berat')
            ->get();

        $grandWeight = $report->sum('total_berat');
        $grandOmset = $report->sum('total_omset');

        return view('sales.report', compact('greenhouse', 'report', 'grandWeight', 'grandOmset', 'from', 'to'));
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $greenhouses = $user->isAdmin()
            ? Greenhouse::query()->orderBy('code')->get()
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

        return view('sales.index', compact('greenhouses', 'salesSummary'));
    }

    public function greenhouse(Greenhouse $greenhouse)
    {
        $todayRevenue = Sale::where('greenhouse_id', $greenhouse->id)
            ->whereDate('created_at', now()->toDateString())
            ->sum('total');

        $monthRevenue = Sale::where('greenhouse_id', $greenhouse->id)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total');

        $totalRevenue = Sale::where('greenhouse_id', $greenhouse->id)->sum('total');
        $transactionCount = Sale::where('greenhouse_id', $greenhouse->id)->count();

        $recentSales = Sale::with(['user', 'items.melonVariety'])
            ->where('greenhouse_id', $greenhouse->id)
            ->latest()
            ->take(10)
            ->get();

        return view('sales.greenhouse', compact(
            'greenhouse',
            'todayRevenue',
            'monthRevenue',
            'totalRevenue',
            'transactionCount',
            'recentSales'
        ));
    }

    public function create(Greenhouse $greenhouse)
    {
        $varieties = MelonVariety::orderBy('name')->get();
        return view('sales.create', compact('greenhouse', 'varieties'));
    }

    public function store(Request $request, Greenhouse $greenhouse)
    {
        $validated = $request->validate([
            'buyer_name' => 'required|string|max:255',
            'sale_items' => 'required|array|min:1',
            'sale_items.*.melon_variety_id' => 'required|exists:melon_varieties,id',
            'sale_items.*.weight_kg' => 'required|numeric|min:0.001',
            'sale_items.*.price_per_kg' => 'required|numeric|min:0',
        ]);

        // Compute total server-side — never trust the form
        $total = 0;
        $items = [];
        foreach ($validated['sale_items'] as $item) {
            $subtotal = $item['weight_kg'] * $item['price_per_kg'];
            $total += $subtotal;
            $items[] = [
                'melon_variety_id' => $item['melon_variety_id'],
                'weight_kg' => $item['weight_kg'],
                'price_per_kg' => $item['price_per_kg'],
                'subtotal' => $subtotal,
            ];
        }

        $sale = Sale::create([
            'greenhouse_id' => $greenhouse->id,
            'user_id' => $request->user()->id,
            'buyer_name' => $validated['buyer_name'],
            'total' => $total,
        ]);

        foreach ($items as $item) {
            $sale->items()->create($item);
        }

        return redirect()
            ->route('sales.greenhouse', $greenhouse)
            ->with('success', 'Penjualan berhasil disimpan.');
    }

    public function show(Request $request, Sale $sale)
    {
        $sale->load(['greenhouse', 'user', 'items.melonVariety']);
        $this->abortIfCannotAccessSale($request, $sale);

        return view('sales.show', compact('sale'));
    }

    public function print(Request $request, Sale $sale)
    {
        $sale->load(['greenhouse', 'user', 'items.melonVariety']);
        $this->abortIfCannotAccessSale($request, $sale);

        return view('sales.print', compact('sale'));
    }

    public function destroy(Request $request, Sale $sale)
    {
        $sale->load('greenhouse');
        $this->abortIfCannotAccessSale($request, $sale);

        $sale->delete(); // cascade deletes sale_items

        return redirect()
            ->route('sales.greenhouse', $sale->greenhouse)
            ->with('success', 'Penjualan berhasil dihapus.');
    }

    private function abortIfCannotAccessSale(Request $request, Sale $sale): void
    {
        if ($request->user()->isAdmin()) {
            return;
        }

        if (!$sale->greenhouse_id || !$request->user()->greenhouses()->where('id', $sale->greenhouse_id)->exists()) {
            abort(403);
        }
    }
}