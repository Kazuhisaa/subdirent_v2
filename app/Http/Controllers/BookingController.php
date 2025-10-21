<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Booking;
use App\Models\Application;
use Illuminate\Http\Request;

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
    public function showAllOccupiedTime($unit_id,$date){
     
        $booking = Booking::where('unit_id',$unit_id)->where('date',$date)->pluck('booking_time');
        return response()->json($booking);
        
    }




 public function store(Request $request)
{
    // Validation rules
    $validated = $request->validate([
        'unit_id' => 'required|exists:units,id',
        'first_name'   => 'required|string|max:50',
        'middle_name'  => 'nullable|string|max:50',
        'last_name'    => 'required|string|max:50',
        'email'        => 'required|email|unique:bookings,email',
        'contact_num'  => 'required|string|max:15',
        'date'         => 'required|date',
        'booking_time'         => 'required|date_format:H:i', // format ng 24-hour time
    ]);

  $application = [
    'first_name'  => $validated['first_name'],
    'middle_name' => $validated['middle_name'] ?? null,
    'last_name'   => $validated['last_name'],
     'email' => $validated['email'],
     'contact_num' => $validated['contact_num'],
     'unit_id'=> $validated['unit_id']
];

  
     
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

public function confirm($id)
{
    $booking = Booking::findOrFail($id);

    // Update booking status
    $booking->status = 'Confirmed';
    $booking->save();

    // --- SIMULA NG PAGBABAGO ---

    // 1. Ihanda ang data para sa bagong Application
    $applicationData = [
        'first_name'  => $booking->first_name,
        'middle_name' => $booking->middle_name,
        'last_name'   => $booking->last_name,
        'email'       => $booking->email,
        'contact_num' => $booking->contact_num,
        'unit_id'     => $booking->unit_id,
        'status'      => 'Pending',
    ];

    // 2. Hanapin ang Unit para makuha ang presyo
    $unit = Unit::find($booking->unit_id);

    if ($unit) {
        // 3. Idagdag ang mga presyo sa data array
        $applicationData['unit_price'] = (float) str_replace(',', '', $unit->unit_price);
    }

    // 4. I-create ang Application gamit ang KUMPLETONG data
    $application = Application::create($applicationData);

    // --- WAKAS NG PAGBABAGO ---

    return response()->json([
        'message' => 'Booking confirmed and moved to Applications!',
        'application' => $application
    ]);
}

 public function archive($id){
            $booking = Booking::findOrFail($id);
            $booking->delete();
            
            return response()-> json([
                'Message' => 'Booking Archived Successfully',
                'data' => $booking
            ]);
    }

    public function viewArchive(){

        $archived = Booking::onlyTrashed()->get();
        return response()->json($archived);
    }




}
