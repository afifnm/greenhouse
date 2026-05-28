<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\MelonVariety;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with(['user', 'items.melonVariety'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $varieties = MelonVariety::orderBy('name')->get();
        return view('sales.create', compact('varieties'));
    }

    public function store(Request $request)
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
            'user_id' => $request->user()->id,
            'buyer_name' => $validated['buyer_name'],
            'total' => $total,
        ]);

        foreach ($items as $item) {
            $sale->items()->create($item);
        }

        return redirect()
            ->route('sales.index')
            ->with('success', 'Penjualan berhasil disimpan.');
    }

    public function show(Sale $sale)
    {
        $sale->load(['user', 'items.melonVariety']);
        return view('sales.show', compact('sale'));
    }

    public function destroy(Sale $sale)
    {
        $sale->delete(); // cascade deletes sale_items
        return redirect()
            ->back()
            ->with('success', 'Penjualan berhasil dihapus.');
    }
}