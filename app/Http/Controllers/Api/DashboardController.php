<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GreenhouseResource;
use App\Models\Greenhouse;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        return $this->managerDashboard($user);
    }

    private function adminDashboard(): JsonResponse
    {
        $totalGreenhouses = Greenhouse::count();
        $activeGreenhouses = Greenhouse::where('is_active', true)->count();

        $totalRevenue = Sale::sum('total');
        $todayRevenue = Sale::whereDate('created_at', now()->toDateString())->sum('total');
        $monthRevenue = Sale::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total');

        $totalTrees = DB::table('trees')->count();
        $aliveTrees = DB::table('trees')->where('status', 'alive')->count();

        $totalFruits = DB::table('fruits')->count();
        $gradeAFruits = DB::table('fruits')->where('grade', 'A')->count();

        $recentGreenhouses = Greenhouse::withCount(['trees', 'sales'])
            ->orderBy('code')
            ->take(5)
            ->get();

        return response()->json([
            'role' => 'admin',
            'stats' => [
                'total_greenhouses'  => $totalGreenhouses,
                'active_greenhouses' => $activeGreenhouses,
                'total_revenue'      => (float) $totalRevenue,
                'today_revenue'      => (float) $todayRevenue,
                'month_revenue'      => (float) $monthRevenue,
                'total_trees'        => $totalTrees,
                'alive_trees'        => $aliveTrees,
                'total_fruits'       => $totalFruits,
                'grade_a_fruits'     => $gradeAFruits,
            ],
            'recent_greenhouses' => GreenhouseResource::collection($recentGreenhouses),
        ]);
    }

    private function managerDashboard($user): JsonResponse
    {
        $greenhouses = $user->greenhouses()->withCount(['trees', 'sales'])->get();

        $greenhouseIds = $greenhouses->pluck('id');

        $todayRevenue = Sale::whereIn('greenhouse_id', $greenhouseIds)
            ->whereDate('created_at', now()->toDateString())
            ->sum('total');

        $monthRevenue = Sale::whereIn('greenhouse_id', $greenhouseIds)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total');

        $gradeAFruits = DB::table('fruits')
            ->join('trees', 'fruits.tree_id', '=', 'trees.id')
            ->whereIn('trees.greenhouse_id', $greenhouseIds)
            ->where('fruits.grade', 'A')
            ->count();

        $deadTreeRate = DB::table('trees')
            ->whereIn('greenhouse_id', $greenhouseIds)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "dead" THEN 1 ELSE 0 END) as dead
            ')
            ->first();

        $totalTrees = (int) ($deadTreeRate->total ?? 0);
        $deadTrees  = (int) ($deadTreeRate->dead ?? 0);

        return response()->json([
            'role' => 'manager',
            'stats' => [
                'today_revenue' => (float) $todayRevenue,
                'month_revenue' => (float) $monthRevenue,
                'grade_a_fruits' => $gradeAFruits,
                'dead_tree_rate' => $totalTrees > 0 ? round(($deadTrees / $totalTrees) * 100, 1) : 0,
            ],
            'greenhouses' => GreenhouseResource::collection($greenhouses),
        ]);
    }
}
