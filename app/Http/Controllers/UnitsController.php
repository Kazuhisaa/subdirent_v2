<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Services\UnitService;
use Illuminate\Http\Request;

class UnitsController extends Controller
{
    protected $unitService;

    public function __construct(UnitService $unitService)
    {
        $this->unitService = $unitService;
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string',
            'unit_code' => 'required|string|unique:units,unit_code',
            'description' => 'nullable|string|max:10000',
            'floor_area' => 'nullable|integer|min:0',
            'lot_size' => 'nullable|integer|min:0',
            'bathroom' => 'nullable|integer|min:0',
            'bedroom' => 'nullable|integer|min:0',
            'monthly_rent' => 'nullable|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'status' => 'nullable|in:available,rented',
            'contract_years' => 'nullable|integer|min:1',
            'files.*' => 'nullable|file|mimes:jpeg,jpg,pdf|max:2048'
        ]);

        $unit = $this->unitService->createUnit($request->all());

        return response()->json(['message' => 'Unit Created', 'unit' => $unit], 201);
    }

    public function index()
    {
        return response()->json($this->unitService->getAvailableUnits());
    }

    public function show($id)
    {
        return response()->json($this->unitService->getUnitById($id));
    }

    public function update(Request $request, Unit $unit)
    {
        $data = $request->all();
        $unit = $this->unitService->updateUnit($unit, $data);

        return response()->json(['message' => 'Unit Updated', 'unit' => $unit]);
    }

    public function archive($id)
    {
        $unit = $this->unitService->archiveUnit(Unit::findOrFail($id));
        return redirect()->route('admin.rooms')->with('success', 'Unit archived.');
    }

    public function unarchive($id)
    {
        $unit = $this->unitService->unarchiveUnit(Unit::findOrFail($id));
        return redirect()->route('admin.rooms')->with('success', 'Unit unarchived.');
    }

    public function search(Request $request)
    {
        $query = $request->input('query', '');
        return response()->json($this->unitService->searchUnits($query));
    }

    public function predict(Request $request)
    {
        $payload = $request->only(['bathroom','bedroom','floor_area','lot_size','year','n_years']);
        return response()->json($this->unitService->predict($payload));
    }

    public function rooms()
    {
        $units = $this->unitService->getAllUnits();
        return view('admin.rooms', ['units' => $units]);
    }

    public function edit($id)
    {
        $unit = Unit::with('tenant')->findOrFail($id);
        return view('admin.edit-unit', ['unit' => $unit]);
    }
}
