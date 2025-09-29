<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;

class BookingController extends Controller
{
    //
   
    public function index(){

        $bookings = Booking::all();
       return response()->json($bookings);
    }
    
    public function show($id){
        $bookings = Booking::findOrfail($id);
        return response()->json($bookings);
    }
  

    //nirereturn nya yung mga occupied time within specific date para mavalidate ni frontend yung mga
    //hindi na available time for booking
    public function showAllOccupiedTime($date,$unit_id){
     
        $booking = Booking::where('unit_id',$unit_id)->where('date',$date)->pluck('time');
        
        return response()->json($booking);
        
    }




 public function store(Request $request)
{
    // Validation rules
    $validated = $request->validate([
        'first_name'   => 'required|string|max:50',
        'middle_name'  => 'nullable|string|max:50',
        'last_name'    => 'required|string|max:50',
        'email'        => 'required|email|unique:bookings,email',
        'contact_num'  => 'required|string|max:15',
        'date'         => 'required|date',
        'time'         => 'required|date_format:H:i', // format ng 24-hour time
    ]);

    // Create record
    $booking = Booking::create($validated);

    // Return JSON response
    return response()->json([
        'message' => 'Booking created successfully!',
        'data' => $booking
    ], 201);
}


public function showByUnitId($unit_id){
    $booking = Booking::where('unit_id',$unit_id)->get(); 
  return response()->json($booking);
        
}

}
