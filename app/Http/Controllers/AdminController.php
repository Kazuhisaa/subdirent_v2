<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Maintenance;

class AdminController extends Controller
{
    public function index()
    {
        // Fake test data for now
        $registeredUsers = 10;
        $roomsForRent = 5;
        $unpaidRent = 2;
        $monthlyIncome = 15000;
        $latestBookings = [];

        $inProgressMaintenanceCount = Maintenance::where('status', 'In Progress')->count();

        return view('admin.home', compact(
            'registeredUsers','roomsForRent','unpaidRent','monthlyIncome','latestBookings','inProgressMaintenanceCount'
        ));
    }
}
