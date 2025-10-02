<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        DB::table('units')->insert([
              [
                'title' => 'Studio Type Apartment',
                'location' => 'Phase 1',
                'unit_code' => 'UNIT-001',
                'description' => 'A cozy studio-type apartment perfect for students and young professionals.',
                'floor_area' => 25,
                'bathroom' => 1,
                'bedroom' => 0,
                'monthly_rent' => 7500.00,
                'unit_price' => 1200000.00,
                'contract_years' =>  13 ,
                'status' => 'Available',
                'files' => json_encode([
                    'images/unit001_front.jpg',
                    'images/unit001_inside.jpg',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '2-Bedroom Condo',
                'location' => 'Phase 2',
                'unit_code' => 'UNIT-002',
                'description' => 'Spacious 2-bedroom condo near MRT station.',
                'floor_area' => 55,
                'bathroom' => 2,
                'bedroom' => 2,
                'monthly_rent' => 15000.00,
                'unit_price' => 3500000.00,
                'contract_years' => 19,
                'status' => 'Available',
                'files' => json_encode([
                    'images/unit002_front.jpg',
                    'images/unit002_livingroom.jpg',
                    'images/unit002_bedroom.jpg',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '1-Bedroom Apartment',
                'location' => 'Phase 3',
                'unit_code' => 'UNIT-003',
                'description' => 'Affordable 1-bedroom apartment close to public transportation.',
                'floor_area' => 35,
                'bathroom' => 1,
                'bedroom' => 1,
                'monthly_rent' => 9500.00,
                'unit_price' => 1800000.00,
                 'contract_years'=>15,
                'status' => 'Available',
                'files' => json_encode([
                    'images/unit003_front.jpg',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
    ]);
    }
}
