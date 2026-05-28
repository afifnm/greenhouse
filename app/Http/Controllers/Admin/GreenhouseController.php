<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Greenhouse;
use App\Models\User;
use Illuminate\Http\Request;

class GreenhouseController extends Controller
{
    public function index(Request $request)
    {
        $greenhouses = Greenhouse::withCount(['trees', 'users'])
            ->with(['users'])
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('code', 'like', "%{$s}%"))
            ->orderBy('code')
            ->paginate(12);

        return view('admin.greenhouses.index', compact('greenhouses'));
    }

    public function create()
    {
        return view('admin.greenhouses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:greenhouses,code',
            'description' => 'nullable|string',
        ]);

        Greenhouse::create($validated);

        return redirect()->route('admin.greenhouses.index')->with('success', 'Greenhouse berhasil dibuat.');
    }

    public function show(Greenhouse $greenhouse)
    {
        $greenhouse->loadCount(['trees', 'trees as alive_trees_count' => fn($q) => $q->where('status', 'alive')]);
        $greenhouse->load(['users', 'materialRequests' => fn($q) => $q->with('user')->latest()->limit(50)]);
        return view('admin.greenhouses.show', compact('greenhouse'));
    }

    public function edit(Greenhouse $greenhouse)
    {
        return view('admin.greenhouses.edit', compact('greenhouse'));
    }

    public function update(Request $request, Greenhouse $greenhouse)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:greenhouses,code,' . $greenhouse->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $greenhouse->update($validated);

        return redirect()->route('admin.greenhouses.index')->with('success', 'Greenhouse berhasil diperbarui.');
    }

    public function destroy(Greenhouse $greenhouse)
    {
        $greenhouse->delete();
        return redirect()->route('admin.greenhouses.index')->with('success', 'Greenhouse berhasil dihapus.');
    }

    public function managers(Greenhouse $greenhouse)
    {
        $managers = User::where('role', 'manager')->where('is_active', true)->get();
        $assigned = $greenhouse->users()->pluck('id')->toArray();
        return view('admin.greenhouses.managers', compact('greenhouse', 'managers', 'assigned'));
    }

    public function assignManagers(Request $request, Greenhouse $greenhouse)
    {
        $request->validate(['managers' => 'array', 'managers.*' => 'exists:users,id']);
        $greenhouse->users()->sync($request->managers ?? []);
        return redirect()->route('admin.greenhouses.show', $greenhouse)->with('success', 'Penanggung jawab berhasil diupdate.');
    }
}
