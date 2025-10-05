<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon; 
class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        DB::table('applications')->insert(

            [

           [
                'first_name'   => 'Juan',
                'middle_name'  => 'Santos',
                'last_name'    => 'Dela Cruz',
                'email'        => 'juan@example.com',
                'contact_num'  => '09171234567',
                'unit_id'      => 1,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now(),
           ],
           [
                 'first_name'   => 'Maria',
                'middle_name'  => 'Reyes',
                'last_name'    => 'Lopez',
                'email'        => 'maria@example.com',
                'contact_num'  => '09179876543',
                'unit_id'      => 2,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now(),
           ],
           [

                 'first_name'   => 'Kenneth',
                'middle_name'  => 'Domdom',
                'last_name'    => 'Reyes',
                'email'        => 'kenneth@example.com',
                'contact_num'  => '09681234567',
                 'unit_id'      => 1,
                 'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now(),

           ]




            ]
        );
    }
}
