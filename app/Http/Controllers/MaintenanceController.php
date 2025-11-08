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
        // 1. Fetch active requests
        $requests = Maintenance::with('tenant.unit') 
            ->withoutTrashed() // Explicitly get only active
            ->orderBy('created_at', 'desc')
            ->get(); 

        // 2. Fetch archived (soft-deleted) requests
        $archivedRequests = Maintenance::with('tenant.unit')
            ->onlyTrashed() // <-- This is the important part
            ->orderBy('deleted_at', 'desc')
            ->get();

        // 3. Pass BOTH variables to the view
        return view('admin.maintenance', [
            'requests' => $requests,
            'archivedRequests' => $archivedRequests // <-- This variable is now included
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
public function archive(Maintenance $maintenance)
    {
        $maintenance->delete(); // This is a soft delete

        return redirect()->route('admin.maintenance')->with('success', 'Maintenance request archived.');
    }

    /**
     * ✅ NEW: Restore an archived maintenance request.
     * This method is required for the "Restore" button in the modal.
     */
    public function restore($id)
    {
        // Find *only* in the trash
        $maintenance = Maintenance::onlyTrashed()->findOrFail($id);
        
        $maintenance->restore(); // Restores the soft-deleted model

        return redirect()->route('admin.maintenance')->with('success', 'Maintenance request restored.');
    }
}