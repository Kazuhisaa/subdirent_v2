<?php

namespace App\Services;

use App\Models\Unit;
use Illuminate\Support\Facades\Http;

class UnitService
{
    public function createUnit(array $data)
    {
        $data['files'] = $this->uploadFiles($data['files'] ?? []);
        $data['status'] = strtolower($data['status'] ?? 'available');

        $data = array_map(fn($v) => is_string($v) ? strip_tags($v) : $v, $data);

        return Unit::create($data);
    }

    public function updateUnit(Unit $unit, array $data)
    {
        $uploadedFiles = $unit->files ?? [];

        if (!empty($data['remove_files'])) {
            $uploadedFiles = $this->removeFiles($data['remove_files'], $uploadedFiles);
        }

        if (!empty($data['files'])) {
            $uploadedFiles = array_merge($uploadedFiles, $this->uploadFiles($data['files']));
        }

        $data['files'] = $uploadedFiles;
        $data = array_map(fn($v) => is_string($v) ? strip_tags($v) : $v, $data);

        $unit->update($data);
        return $unit;
    }

    public function uploadFiles(array $files): array
    {
        $uploaded = [];
        $uploadPath = public_path('uploads/units');
        if (!file_exists($uploadPath)) mkdir($uploadPath, 0777, true);

        foreach ($files as $file) {
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);
            $uploaded[] = 'uploads/units/' . $filename;
        }

        return $uploaded;
    }

    public function removeFiles(array $toRemove, array $existing): array
    {
        foreach ($toRemove as $file) {
            if (($key = array_search($file, $existing)) !== false) {
                unset($existing[$key]);
                $fullPath = public_path($file);
                if (file_exists($fullPath)) @unlink($fullPath);
            }
        }
        return array_values($existing);
    }

    public function getAllUnits()
    {
        $units = Unit::all();
        $units->transform(function ($unit) {
            $files = is_array($unit->files) ? $unit->files : json_decode($unit->files, true);
            $unit->files = $files ? array_map(fn($f) => asset($f), $files) : [];
            $unit->phase = $unit->location; // Assuming 'phase' is derived from 'location' for consistency
            return $unit;
        });
        return $units;
    }

    public function getAvailableUnits()
    {
        $units = Unit::where('status', 'available')->get();
        $units->transform(function ($unit) {
            $files = is_array($unit->files) ? $unit->files : json_decode($unit->files, true);
            $unit->files = $files ? array_map(fn($f) => asset($f), $files) : [];
            $unit->phase = $unit->location;
            return $unit;
        });
        return $units;
    }

    public function getUnitById(int $id)
    {
        $unit = Unit::findOrFail($id);
        $unit->files = is_array($unit->files) ? $unit->files : [];
        return $unit;
    }

    public function archiveUnit(Unit $unit)
    {
        $unit->status = 'archived';
        $unit->save();
        return $unit;
    }

    public function unarchiveUnit(Unit $unit)
    {
        $unit->status = 'available';
        $unit->save();
        return $unit;
    }

    public function searchUnits(string $query)
    {
        return Unit::query()
            ->where('title', 'LIKE', "%{$query}%")
            ->orWhere('location', 'LIKE', "%{$query}%")
            ->orWhere('unit_code', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->get();
    }

    public function predict(array $payload)
    {
        $flaskUrl = 'https://unit-api-yxz2.onrender.com/predict?fbclid=IwY2xjawPPHIhleHRuA2FlbQIxMABicmlkETFOQk54NVhWalBnNEptVkl2c3J0YwZhcHBfaWQQMjIyMDM5MTc4ODIwMDg5MgABHt-wb_kyQadutzDg_hohfwt3VjunjwyQd7DUrRb_WEdMQLklYhWFdpRtHw5P_aem_mzC_Xk9stdTcQ5k-05jIqw';
        $response = Http::post($flaskUrl, $payload);
        return $response->json();
    }
}
    