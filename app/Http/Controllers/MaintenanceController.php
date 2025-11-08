<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Maintenance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; // Import Rule
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

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
            ->limit(3)
            ->get();

        return view('tenant.maintenance', compact('recentRequests'));
    }

    /**
     * Handle new maintenance request submission from a tenant
     */
    public function store(Request $request)
    {
        // === MODIFIED VALIDATION LOGIC ===
        $rules = [
            'urgency' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        // Conditional requirement for Category and Description
        if ($request->urgency === 'Others') {
            // If Urgency is 'Others': Category is nullable (since dropdown is disabled), Description is required.
            $rules['category'] = 'nullable|string|max:255';
            $rules['description'] = 'required|string'; 
        } else {
            // If Urgency is Low, Medium, or High: Category is required, Description is optional/nullable.
            $rules['category'] = 'required|string|max:255';
            $rules['description'] = 'nullable|string';
        }
        
        $request->validate($rules);
        // === END MODIFIED VALIDATION LOGIC ===


        $user = Auth::user();
        if (!$user || !$user->tenant) {
            return redirect()->back()->with('error', 'Could not find your tenant account.');
        }

        $path = null;
        // 4. HANDLE THE PHOTO UPLOAD (MODIFIED BLOCK)
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            // NOTE: Changed 'public/uploads/maintenances/' to 'uploads/maintenances/' 
            // to correctly store the public web path in the database.
            $uploadDir = 'uploads/maintenances/'; 
            $uploadPath = public_path($uploadDir); 
            
            // Create directory if it doesn't exist
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            // Create a unique filename
            $filename = time() . '-' . $user->tenant->id . '.' . $file->getClientOriginalExtension();
            $path = $uploadDir . $filename; // Path to save in DB

            // Move the new file to public/uploads/maintenances
            $file->move($uploadPath, $filename);
        }
        // END PHOTO UPLOAD BLOCK

        // Handle the category value when 'Others' is selected and the dropdown is disabled.
        $category = $request->category;
        if ($request->urgency === 'Others' || empty($category)) {
            $category = 'N/A - See Description'; 
        }
        
        // === RESTORED: SAVING THE MAINTENANCE RECORD ===
        Maintenance::create([
            'tenant_id' => $user->tenant->id,
            'category' => $category,
            'urgency' => $request->urgency,
            'description' => $request->description,
            'photo' => $path, // Contains the saved path or null
            'status' => 'Pending',
        ]);
        // ===============================================

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

        // === MODIFIED LOGIC START ===
        if ($validated['status'] == 'Pending') {
            // Pending requests should not have a scheduled date
            $validated['scheduled_date'] = null;
        } elseif ($validated['status'] == 'Completed') {
            // Completed requests should record the completion date/time
            // If scheduled_date was provided (e.g., if updating from In Progress), use it.
            // Otherwise, use the current time (completion time).
            if (empty($validated['scheduled_date'])) {
                 $validated['scheduled_date'] = Carbon::now();
            }
        }
        // If status is 'In Progress', the scheduled_date is handled by the validation's requiredIf rule.
        // === MODIFIED LOGIC END ===

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