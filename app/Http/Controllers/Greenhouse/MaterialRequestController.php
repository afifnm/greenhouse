<?php

namespace App\Http\Controllers\Greenhouse;

use App\Http\Controllers\Controller;
use App\Models\MaterialRequest;
use Illuminate\Http\Request;

class MaterialRequestController extends Controller
{
    public function index(\App\Models\Greenhouse $greenhouse)
    {
        $materialRequests = $greenhouse->materialRequests()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('greenhouse.material-requests', compact('greenhouse', 'materialRequests'));
    }

    public function store(Request $request, \App\Models\Greenhouse $greenhouse)
    {
        $validated = $request->validate([
            'material_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['greenhouse_id'] = $greenhouse->id;
        $validated['user_id'] = $request->user()->id;
        $validated['status'] = 'pending';

        MaterialRequest::create($validated);

        return redirect()
            ->back()
            ->with('success', 'Permintaan material berhasil diajukan.');
    }

    public function update(Request $request, \App\Models\Greenhouse $greenhouse, MaterialRequest $materialRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,fulfilled',
        ]);

        $materialRequest->update($validated);

        return redirect()
            ->back()
            ->with('success', 'Status permintaan berhasil diupdate.');
    }

    public function destroy(\App\Models\Greenhouse $greenhouse, MaterialRequest $materialRequest)
    {
        $materialRequest->delete();

        return redirect()
            ->back()
            ->with('success', 'Permintaan material berhasil dihapus.');
    }
}