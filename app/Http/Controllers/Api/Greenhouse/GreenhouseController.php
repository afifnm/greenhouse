<?php

namespace App\Http\Controllers\Api\Greenhouse;

use App\Http\Controllers\Controller;
use App\Http\Resources\TreeResource;
use App\Models\Greenhouse;
use Illuminate\Http\JsonResponse;

class GreenhouseController extends Controller
{
    public function show(Greenhouse $greenhouse): JsonResponse
    {
        $greenhouse->loadCount([
            'trees',
            'trees as alive_trees_count' => fn($q) => $q->where('status', 'alive'),
        ]);

        $totalTrees = (int) ($greenhouse->trees_count ?? 0);
        $aliveTrees = (int) ($greenhouse->alive_trees_count ?? 0);

        $recentTrees = $greenhouse->trees()
            ->with('variety')
            ->withCount('fruits')
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'id'           => $greenhouse->id,
            'name'         => $greenhouse->name,
            'code'         => $greenhouse->code,
            'description'  => $greenhouse->description,
            'is_active'    => $greenhouse->is_active,
            'total_trees'  => $totalTrees,
            'alive_trees'  => $aliveTrees,
            'dead_trees'   => $totalTrees - $aliveTrees,
            'created_at'   => $greenhouse->created_at,
            'recent_trees' => TreeResource::collection($recentTrees),
        ]);
    }
}
