<?php

namespace App\Http\Controllers;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; 
class UnitsController extends Controller
{

    public function __construct(){

    }
            public function store(Request $request)
            {   
                                $user = $request->user();

                if (!$user || !$user->tokenCan('admin')) {
                    return response()->json(['message' => 'Unauthorized'], 403);
                }
 

                $request->validate([
                    'title' => 'required|string|max:255',
                    'location' => 'required|string',
                    'unit_code' => 'required|string|unique:units,unit_code',
                    'description' => 'nullable|string|max:10000',
                    'floor_area' => 'nullable|integer|min:0',
                    'bathroom' => 'nullable|integer|min:0',
                    'bedroom' => 'nullable|integer|min:0',
                    'monthly_rent' => 'nullable|numeric|min:0',
                    'unit_price' => 'required|numeric|min:0',
                    'status' => 'nullable|in:available,rented',
                    'contract_years' => 'nullable|integer|min:1',
                    'files.*' => 'nullable|file|mimes:jpeg,jpg,pdf|max:2048'
                ]);

                $uploadPath = public_path('uploads/units');
                if (!file_exists($uploadPath)) mkdir($uploadPath, 0777, true);

                $uploadedFiles = [];
                if ($request->hasFile('files')) {
                    foreach ($request->file('files') as $file) {
                        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->move($uploadPath, $filename);
                        $uploadedFiles[] = 'uploads/units/' . $filename;
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
                    'status' => strtolower($request->status ?? 'available'),
                    'contract_years' => $request->contract_years,
                    'files' => $uploadedFiles,
                ]);

                return response()->json([
                        'message' => 'Unit Created Successfully!',
                        'unit' => $unit
                    ], 201);       
         }  

    public function index()
{
    $units = Unit::where('status', 'available')->get();

    // Convert file paths to full URLs
    $units->transform(function ($unit) {
        // Ensure $unit->files is array
        $files = is_array($unit->files) ? $unit->files : json_decode($unit->files, true);
        if (!$files) $files = [];

        // convert each to full URL (ex: http://127.0.0.1:8000/uploads/units/...)
        $unit->files = array_map(fn($file) => asset($file), $files);
        $unit->phase = $unit->location; 
        return $unit;
    });

    return response()->json($units);
}


    public function show($id)
    {
        
        $unit = Unit::findOrFail($id);
        if (!is_array($unit->files)) {
        $unit->files = [];
}
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
    public function publicUnits() {
    $units = Unit::all();
    return view('units', compact('units')); // ✅ ipapasa ang $units sa view
}

        public function unarchive($id)
    {
        $unit = Unit::findOrFail($id);
        $unit->status = 'available';
        $unit->save();

        return redirect()->route('admin.rooms')->with('success', 'Unit unarchived successfully!');
    }
        public function search(Request $request)
        {
            $query = $request->input('query', '');

            $units = Unit::query()
                ->where('title', 'LIKE', "%{$query}%")
                ->orWhere('location', 'LIKE', "%{$query}%")
                ->orWhere('unit_code', 'LIKE', "%{$query}%")
                ->orWhere('description', 'LIKE', "%{$query}%")
                ->get();

            return response()->json($units);
        }

        public function available()
    {
        // Only return available units
        $units = Unit::where('status', 'available')->get();
        return response()->json($units);
    }

 
 public function predict(Request $request)
    {
        $flaskUrl = 'http://127.0.0.1:5000/predict'; // your Flask API endpoint

         //send POST request with JSON
         $response = Http::post($flaskUrl, [
            'bathroom'   => $request->bathroom,
            'bedroom'    => $request->bedroom,
            'floor_area' => $request->floor_area,
            'lot_size'   => $request->lot_size,
            'year'       => $request->year,
            'n_years'    => $request->n_years ?? 5
        ]);

        return $response->json();
    }

    
}



