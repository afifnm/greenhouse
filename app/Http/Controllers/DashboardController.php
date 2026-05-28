<?php

namespace App\Http\Controllers;

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

        return view('dashboard', compact('greenhouses'));
    }
}