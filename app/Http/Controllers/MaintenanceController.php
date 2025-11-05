<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Maintenance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaintenanceController extends Controller
{
    // Show maintenance request page
    public function index()
{
    $tenantId = auth()->id();

    // Get the 5 most recent maintenance requests for this tenant
    $recentRequests = \App\Models\Maintenance::where('tenant_id', $tenantId)
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

    return view('tenant.maintenance', compact('recentRequests'));
}


    // Handle new maintenance request submission
    public function store(Request $request)
{
    $request->validate([
        'category' => 'required|string|max:255',
        'urgency' => 'required|string',
        'description' => 'required|string',
        'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    $tenant = Auth::user();

    $path = null;
    if ($request->hasFile('photo')) {
        // ✅ Save inside storage/app/public/maintenance_photos
        $path = $request->file('photo')->store('maintenance_photos', 'public');
    }

    Maintenance::create([
        'tenant_id' => $tenant->id,
        'category' => $request->category,
        'urgency' => $request->urgency,
        'description' => $request->description,
        'photo' => $path,
        'status' => 'Pending',
    ]);

    return redirect()->back()->with('success', 'Maintenance request submitted successfully!');
}

}
