<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Maintenance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule; // Import Rule

class MaintenanceController extends Controller
{
    // ===========================================
    // TENANT-FACING METHODS
    // ===========================================

    /**
     * Show maintenance request page for the logged-in tenant
     */
public function showIndex()
{
    $maintenance = Maintenance::with(['tenant.unit'])->get();
    return response()->json($maintenance);
}


    public function index()
    {
        $user = Auth::user();
        if (!$user || !$user->tenant) {
            return view('tenant.maintenance', ['recentRequests' => collect()]);
        }
        $tenantId = $user->tenant->id;

        $recentRequests = \App\Models\Maintenance::where('tenant_id', $tenantId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('tenant.maintenance', compact('recentRequests'));
    }

    /**
     * Handle new maintenance request submission from a tenant
     */
    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'urgency' => 'required|string',
            'description' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = Auth::user();
        if (!$user || !$user->tenant) {
            return redirect()->back()->with('error', 'Could not find your tenant account.');
        }

        $path = null;
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('maintenance_photos', 'public');
        }

        Maintenance::create([
            'tenant_id' => $user->tenant->id,
            'category' => $request->category,
            'urgency' => $request->urgency,
            'description' => $request->description,
            'photo' => $path,
            'status' => 'Pending',
        ]);

        return redirect()->back()->with('success', 'Maintenance request submitted successfully!');
    }

    // ===========================================
    // ADMIN-FACING METHODS
    // ===========================================

    /**
     * Display all maintenance requests for the admin.
     * This is the method that provides the $requests variable.
     */
    public function adminIndex()
    {
        // This line fetches all requests and their tenant/unit info
        $requests = Maintenance::with('tenant.unit') 
            ->orderBy('created_at', 'desc')
            ->get(); 

        // This line passes the $requests variable to the view
        return view('admin.maintenance', [
            'requests' => $requests
        ]);
    }

    /**
     * Update the status, scheduled date, and notes for a maintenance request.
     */
    public function update(Request $request, Maintenance $maintenance)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['Pending', 'In Progress', 'Completed'])],
            // Require scheduled_date ONLY IF status is 'In Progress'
            'scheduled_date' => [
                Rule::requiredIf($request->status == 'In Progress'),
                'nullable',
                'date'
            ],
            'notes' => 'nullable|string',
        ]);

        // If status is NOT 'In Progress', force scheduled_date to be null
        if ($validated['status'] != 'In Progress') {
            $validated['scheduled_date'] = null;
        }

        $maintenance->update($validated);

        // === FIX: Changed route name from 'admin.maintenance.index' to 'admin.maintenance' ===
        return redirect()->route('admin.maintenance')->with('success', 'Maintenance request updated successfully.');
    }

    /**
     * Archive a maintenance request (Soft Delete).
     */
  public function archive($id)
    {
        $maintenance = Maintenance::findOrFail($id);
        $maintenance->delete();

       return response()->json([
        'success' => true,
        'message' => 'Maintenance request archived successfully.'
    ]);
}

    /**
     * Get all archived (soft-deleted) maintenance requests
     */
    public function archived()
    {
        $archivedRequests = Maintenance::onlyTrashed()
            ->with('tenant.unit')
            ->orderBy('deleted_at', 'desc')
            ->get();

        return response()->json($archivedRequests);
    }

    /**
     * Restore a previously archived maintenance request
     */
    public function restore($id)
    {
        $maintenance = Maintenance::onlyTrashed()->findOrFail($id);
        $maintenance->restore();

        return response()->json(['success' => true, 'message' => 'Request restored successfully.']);
    }
}