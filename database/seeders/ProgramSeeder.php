<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colleges = DB::table('colleges')
            ->pluck('id', 'code');

        DB::table('programs')->insert([
            // CEIT
            [
                'code' => 'BSCS',
                'name' => 'Bachelor of Science in Computer Science',
                'college_id' => $colleges['CEIT'],
            ],
            [
                'code' => 'BSIS',
                'name' => 'Bachelor of Science in Information Systems',
                'college_id' => $colleges['CEIT'],
            ],
            [
                'code' => 'BSIT',
                'name' => 'Bachelor of Science in Information Technology',
                'college_id' => $colleges['CEIT'],
            ],
            [
                'code' => 'BSCpE',
                'name' => 'Bachelor of Science in Computer Engineering',
                'college_id' => $colleges['CEIT'],
            ],
            [
                'code' => 'BSCE',
                'name' => 'Bachelor of Science in Civil Engineering',
                'college_id' => $colleges['CEIT'],
            ],

            // CAS
            [
                'code' => 'BSBIO',
                'name' => 'Bachelor of Science in Biology',
                'college_id' => $colleges['CAS'],
            ],
            [
                'code' => 'BSPSY',
                'name' => 'Bachelor of Science in Psychology',
                'college_id' => $colleges['CAS'],
            ],

            // CED
            [
                'code' => 'BEED',
                'name' => 'Bachelor of Elementary Education',
                'college_id' => $colleges['CED'],
            ],
            [
                'code' => 'BSED',
                'name' => 'Bachelor of Secondary Education',
                'college_id' => $colleges['CED'],
            ],

            // CEMDS
            [
                'code' => 'BSBA',
                'name' => 'Bachelor of Science in Business Administration',
                'college_id' => $colleges['CEMDS'],
            ],
            [
                'code' => 'BSECON',
                'name' => 'Bachelor of Science in Economics',
                'college_id' => $colleges['CEMDS'],
            ],

            // CAFENR
            [
                'code' => 'BSA',
                'name' => 'Bachelor of Science in Agriculture',
                'college_id' => $colleges['CAFENR'],
            ],
            [
                'code' => 'BSF',
                'name' => 'Bachelor of Science in Forestry',
                'college_id' => $colleges['CAFENR'],
            ],

            // CON
            [
                'code' => 'BSN',
                'name' => 'Bachelor of Science in Nursing',
                'college_id' => $colleges['CON'],
            ],

            // CSPEAR
            [
                'code' => 'BPE',
                'name' => 'Bachelor of Physical Education',
                'college_id' => $colleges['CSPEAR'],
            ],

            // CCJ
            [
                'code' => 'BSCRIM',
                'name' => 'Bachelor of Science in Criminology',
                'college_id' => $colleges['CCJ'],
            ],
            [
                'code' => 'BSISM',
                'name' => 'Bachelor of Science in Industrial Security Management',
                'college_id' => $colleges['CCJ'],
            ],

            // CVM
            [
                'code' => 'DVM',
                'name' => 'Doctor of Veterinary Medicine',
                'college_id' => $colleges['CVM'],
            ],
        ]);
    }
}
