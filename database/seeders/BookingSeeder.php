<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon; 
class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        DB::table('bookings')->insert([
   [
                'unit_id'      => 1,                     // must exist in units table
                'first_name'   => 'Juan',
                'middle_name'  => 'Santos',
                'last_name'    => 'Dela Cruz',
                'email'        => 'juan@example.com',
                'contact_num'  => '09171234567',
                'date'         => '2025-10-05',
                'time' => '10:00:00',
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now(),
            ],
               [
                'unit_id'      => 2,
                'first_name'   => 'Maria',
                'middle_name'  => 'Reyes',
                'last_name'    => 'Lopez',
                'email'        => 'maria@example.com',
                'contact_num'  => '09179876543',
                'date'         => '2025-10-06',
                'time' => '14:30:00',
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now(),
            ],
             [
                'unit_id'      => 1,
                'first_name'   => 'Kenneth',
                'middle_name'  => 'Domdom',
                'last_name'    => 'Reyes',
                'email'        => 'kenneth@example.com',
                'contact_num'  => '09681234567',
                'date'         => '2025-10-07',
                'time' => '09:15:00',
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now(),
            ]


        ]);
    }
}
