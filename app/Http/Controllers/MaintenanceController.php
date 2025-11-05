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
        // $tenantId = auth()->id(); // <-- OLD (WRONG)
        
        // --- START FIX ---
        // Get the logged-in user, then get their related tenant record's ID
        $user = Auth::user();
        if (!$user || !$user->tenant) {
            // Handle case where user might not have a tenant record
            // This is a safety check.
            return view('tenant.maintenance', ['recentRequests' => collect()]);
        }
        $tenantId = $user->tenant->id;
        // --- END FIX ---


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

        // $tenant = Auth::user(); // <-- OLD (WRONG)
        
        // --- START FIX ---
        $user = Auth::user();
        if (!$user || !$user->tenant) {
            return redirect()->back()->with('error', 'Could not find your tenant account.');
        }
        // --- END FIX ---

        $path = null;
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('maintenance_photos', 'public');
        }

        Maintenance::create([
            // 'tenant_id' => $tenant->id, // <-- OLD (WRONG)
            'tenant_id' => $user->tenant->id, // <-- NEW (CORRECT)
            'category' => $request->category,
            'urgency' => $request->urgency,
            'description' => $request->description,
            'photo' => $path,
            'status' => 'Pending',
        ]);

        return redirect()->back()->with('success', 'Maintenance request submitted successfully!');
    }

}