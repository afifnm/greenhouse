<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Fruit;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.greenhouses.index');
        }

        $greenhouses = $user->greenhouses()
            ->withCount([
                'trees',
                'trees as alive_trees_count' => fn($q) => $q->where('status', 'alive')
            ])
            ->get();

        $ghIds = $greenhouses->pluck('id');

        $todayRevenue = Sale::whereIn('greenhouse_id', $ghIds)
            ->whereDate('created_at', now()->toDateString())
            ->sum('total');

        $gradeAToday = Fruit::join('trees', 'fruits.tree_id', '=', 'trees.id')
            ->whereIn('trees.greenhouse_id', $ghIds)
            ->where('trees.status', 'alive')
            ->where('fruits.grade', 'A')
            ->whereDate('fruits.created_at', now()->toDateString())
            ->count();

        $totalTrees = $greenhouses->sum('trees_count');
        $aliveTrees = $greenhouses->sum('alive_trees_count');
        $deadTrees = $totalTrees - $aliveTrees;
        $deadRate = $totalTrees > 0 ? round(($deadTrees / $totalTrees) * 100, 1) : 0;

        return view('dashboard', compact(
            'greenhouses',
            'todayRevenue',
            'gradeAToday',
            'deadRate'
        ));
    }
}