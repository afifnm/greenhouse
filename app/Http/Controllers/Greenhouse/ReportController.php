<?php

namespace App\Http\Controllers\Greenhouse;

use App\Http\Controllers\Controller;
use App\Models\Greenhouse;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function show(Greenhouse $greenhouse)
    {
        $treeStats = DB::table('trees')
            ->where('greenhouse_id', $greenhouse->id)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "alive" THEN 1 ELSE 0 END) as alive,
                SUM(CASE WHEN status = "dead" THEN 1 ELSE 0 END) as dead
            ')
            ->first();

        $fruitStats = DB::table('fruits')
            ->join('trees', 'fruits.tree_id', '=', 'trees.id')
            ->where('trees.greenhouse_id', $greenhouse->id)
            ->where('trees.status', 'alive')
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN fruits.condition = "good" THEN 1 ELSE 0 END) as good,
                SUM(CASE WHEN fruits.condition = "rotten" THEN 1 ELSE 0 END) as rotten,
                SUM(CASE WHEN fruits.grade = "A" THEN 1 ELSE 0 END) as grade_a,
                SUM(CASE WHEN fruits.grade = "B" THEN 1 ELSE 0 END) as grade_b,
                SUM(CASE WHEN fruits.grade = "C" THEN 1 ELSE 0 END) as grade_c,
                SUM(CASE WHEN fruits.grade = "D" THEN 1 ELSE 0 END) as grade_d,
                COALESCE(SUM(fruits.weight), 0) as total_weight,
                COALESCE(AVG(fruits.weight), 0) as avg_weight
            ')
            ->first();

        $treesByVariety = DB::table('trees')
            ->leftJoin('melon_varieties', 'trees.melon_variety_id', '=', 'melon_varieties.id')
            ->where('trees.greenhouse_id', $greenhouse->id)
            ->selectRaw('
                COALESCE(melon_varieties.name, "Tanpa Varietas") as variety_name,
                COUNT(trees.id) as total_trees,
                SUM(CASE WHEN trees.status = "alive" THEN 1 ELSE 0 END) as alive_trees,
                SUM(CASE WHEN trees.status = "dead" THEN 1 ELSE 0 END) as dead_trees
            ')
            ->groupBy('melon_varieties.id', 'melon_varieties.name')
            ->orderBy('variety_name')
            ->get();

        $fruitsByVariety = DB::table('fruits')
            ->join('trees', 'fruits.tree_id', '=', 'trees.id')
            ->leftJoin('melon_varieties', 'trees.melon_variety_id', '=', 'melon_varieties.id')
            ->where('trees.greenhouse_id', $greenhouse->id)
            ->where('trees.status', 'alive')
            ->selectRaw('
                COALESCE(melon_varieties.name, "Tanpa Varietas") as variety_name,
                COUNT(fruits.id) as total_fruits,
                SUM(CASE WHEN fruits.condition = "good" THEN 1 ELSE 0 END) as good_fruits,
                SUM(CASE WHEN fruits.condition = "rotten" THEN 1 ELSE 0 END) as rotten_fruits,
                SUM(CASE WHEN fruits.grade = "A" THEN 1 ELSE 0 END) as grade_a,
                SUM(CASE WHEN fruits.grade = "B" THEN 1 ELSE 0 END) as grade_b,
                SUM(CASE WHEN fruits.grade = "C" THEN 1 ELSE 0 END) as grade_c,
                SUM(CASE WHEN fruits.grade = "D" THEN 1 ELSE 0 END) as grade_d,
                COALESCE(SUM(fruits.weight), 0) as total_weight
            ')
            ->groupBy('melon_varieties.id', 'melon_varieties.name')
            ->orderByDesc('total_fruits')
            ->get();

        // Weight by grade
        $weightByGrade = DB::table('fruits')
            ->join('trees', 'fruits.tree_id', '=', 'trees.id')
            ->where('trees.greenhouse_id', $greenhouse->id)
            ->where('trees.status', 'alive')
            ->whereNotNull('fruits.weight')
            ->selectRaw('
                fruits.grade,
                COUNT(*) as count,
                COALESCE(SUM(fruits.weight), 0) as total_weight,
                COALESCE(AVG(fruits.weight), 0) as avg_weight
            ')
            ->groupBy('fruits.grade')
            ->get();

        // Weight by variety and grade (matrix)
        $weightByVarietyGrade = DB::table('fruits')
            ->join('trees', 'fruits.tree_id', '=', 'trees.id')
            ->leftJoin('melon_varieties', 'trees.melon_variety_id', '=', 'melon_varieties.id')
            ->where('trees.greenhouse_id', $greenhouse->id)
            ->where('trees.status', 'alive')
            ->whereNotNull('fruits.weight')
            ->selectRaw('
                COALESCE(melon_varieties.name, "Tanpa Varietas") as variety_name,
                fruits.grade,
                COUNT(*) as fruit_count,
                COALESCE(SUM(fruits.weight), 0) as total_weight,
                COALESCE(AVG(fruits.weight), 0) as avg_weight
            ')
            ->groupBy('melon_varieties.id', 'melon_varieties.name', 'fruits.grade')
            ->orderBy('variety_name')
            ->orderBy('fruits.grade')
            ->get();

        $totalTrees = (int) ($treeStats->total ?? 0);
        $aliveTrees = (int) ($treeStats->alive ?? 0);
        $deadTrees = (int) ($treeStats->dead ?? 0);
        $totalFruits = (int) ($fruitStats->total ?? 0);
        $goodFruits = (int) ($fruitStats->good ?? 0);
        $rottenFruits = (int) ($fruitStats->rotten ?? 0);

        $summary = [
            'total_trees' => $totalTrees,
            'alive_trees' => $aliveTrees,
            'dead_trees' => $deadTrees,
            'alive_rate' => $totalTrees > 0 ? round(($aliveTrees / $totalTrees) * 100, 1) : 0,
            'dead_rate' => $totalTrees > 0 ? round(($deadTrees / $totalTrees) * 100, 1) : 0,
            'total_fruits' => $totalFruits,
            'good_fruits' => $goodFruits,
            'rotten_fruits' => $rottenFruits,
            'good_rate' => $totalFruits > 0 ? round(($goodFruits / $totalFruits) * 100, 1) : 0,
            'rotten_rate' => $totalFruits > 0 ? round(($rottenFruits / $totalFruits) * 100, 1) : 0,
            'fruit_per_alive_tree' => $aliveTrees > 0 ? round($totalFruits / $aliveTrees, 2) : 0,
            'grade_a' => (int) ($fruitStats->grade_a ?? 0),
            'grade_b' => (int) ($fruitStats->grade_b ?? 0),
            'grade_c' => (int) ($fruitStats->grade_c ?? 0),
            'grade_d' => (int) ($fruitStats->grade_d ?? 0),
            'total_weight' => round((float) $fruitStats->total_weight, 2),
            'avg_weight' => round((float) $fruitStats->avg_weight, 2),
        ];

        $topVariety = $fruitsByVariety->first();

        return view('greenhouse.report', compact(
            'greenhouse',
            'summary',
            'treesByVariety',
            'fruitsByVariety',
            'weightByGrade',
            'weightByVarietyGrade',
            'topVariety'
        ));
    }
}
