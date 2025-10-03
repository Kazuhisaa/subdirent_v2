<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitsController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string',
            'unit_code' => 'required|string',
            'description' => 'required|string|max:10000',
            'floor_area' => 'nullable|integer|min:0',
            'bathroom' => 'nullable|integer|min:0',
            'bedroom' => 'nullable|integer|min:0',
            'monthly_rent' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'status' => 'nullable|string',
            'files.*' => 'nullable|file|mimes:jpeg,jpg,pdf|max:2048'
        ]);

        // Ensure upload directory exists
        $uploadPath = public_path('uploads/units');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $uploadedFiles = [];

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($uploadPath, $filename);
                $uploadedFiles[] = 'uploads/units/' . $filename; // relative path
            }
        }

        $unit = Unit::create([
            'title' => strip_tags($request->title),
            'location' => strip_tags($request->location),
            'unit_code' => strip_tags($request->unit_code),
            'description' => strip_tags($request->description),
            'floor_area' => $request->floor_area,
            'bathroom' => $request->bathroom,
            'bedroom' => $request->bedroom,
            'monthly_rent' => $request->monthly_rent,
            'unit_price' => $request->unit_price,
            'status' => $request->status ?? 'Available',
            'files' => $uploadedFiles, // ✅ stored as JSON in DB
        ]);

        return redirect()
            ->route('admin.addroom')
            ->with('success', 'Unit Created Successfully!');
    }

    public function index()
    {
        $unit = Unit::all();
        return response()->json($unit);
    }

    public function show($id)
    {
        $unit = Unit::findOrFail($id);
        return response()->json($unit);
    }

    public function update(Request $request, Unit $unit)
    {
        $credentials = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string',
            'unit_code' => 'required|string',
            'description' => 'required|string|max:10000',
            'floor_area' => 'nullable|integer|min:0',
            'bathroom' => 'nullable|integer|min:0',
            'bedroom' => 'nullable|integer|min:0',
            'monthly_rent' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'status' => 'nullable|string',
            'files.*' => 'nullable|file|mimes:jpeg,jpg,pdf|max:2048',
            'remove_files' => 'nullable|array',
            'remove_files.*' => 'string',
        ]);

        $uploadedFiles = $unit->files ?? [];

        // Remove files if requested
        if ($request->has('remove_files')) {
            foreach ($request->remove_files as $fileToRemove) {
                if (($key = array_search($fileToRemove, $uploadedFiles)) !== false) {
                    unset($uploadedFiles[$key]);
                    $fullPath = public_path($fileToRemove);
                    if (file_exists($fullPath)) {
                        @unlink($fullPath); // suppress errors
                    }
                }
            }
            $uploadedFiles = array_values($uploadedFiles);
        }

        // Ensure the upload folder exists
        $uploadPath = public_path('uploads/units');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Handle new uploaded files
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($uploadPath, $filename);
                $uploadedFiles[] = 'uploads/units/' . $filename; // store relative path
            }
        }

        $credentials['files'] = $uploadedFiles;

        $unit->update($credentials);

        return response()->json([
            'message' => 'Unit Updated Successfully',
            'unit' => $unit
        ]);
    }




        public function rooms()
    {
        // Fetch all units
        $units = Unit::all();

        // Return the Blade view with data
        return view('admin.rooms', compact('units'));
    }
    public function edit($id)
    {
        $unit = Unit::findOrFail($id);
        return view('admin.edit-unit', compact('unit'));
    }

    public function archive($id)
    {
        $unit = Unit::findOrFail($id);
        $unit->status = 'archived';
        $unit->save();

        return redirect()->route('admin.rooms')->with('success', 'Unit archived successfully.');
    }

}

