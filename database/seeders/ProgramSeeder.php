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

        $programs = [
            // CAFENR
            ['code' => 'BAE', 'name' => 'Bachelor of Agricultural Entrepreneurship', 'college_id' => $colleges['CAFENR']],
            ['code' => 'BSA', 'name' => 'BS Agriculture', 'college_id' => $colleges['CAFENR']],
            ['code' => 'BSES', 'name' => 'BS Environmental Science', 'college_id' => $colleges['CAFENR']],
            ['code' => 'BSFT', 'name' => 'BS Food Technology', 'college_id' => $colleges['CAFENR']],

            // CAS
            ['code' => 'BAELS', 'name' => 'BA English Language Studies', 'college_id' => $colleges['CAS']],
            ['code' => 'BAJ', 'name' => 'BA Journalism', 'college_id' => $colleges['CAS']],
            ['code' => 'BAPS', 'name' => 'BA Political Science', 'college_id' => $colleges['CAS']],
            ['code' => 'BSAM', 'name' => 'BS Applied Mathematics', 'college_id' => $colleges['CAS']],
            ['code' => 'BSBIO', 'name' => 'BS Biology', 'college_id' => $colleges['CAS']],
            ['code' => 'BSPSY', 'name' => 'BS Psychology', 'college_id' => $colleges['CAS']],
            ['code' => 'BSSW', 'name' => 'BS Social Work', 'college_id' => $colleges['CAS']],

            // CCJ
            ['code' => 'BSCRIM', 'name' => 'BS Criminology', 'college_id' => $colleges['CCJ']],
            ['code' => 'BSISM', 'name' => 'BS Industrial Security Management', 'college_id' => $colleges['CCJ']],

            // CEMDS
            ['code' => 'BSACC', 'name' => 'BS Accountancy', 'college_id' => $colleges['CEMDS']],
            ['code' => 'BSBM', 'name' => 'BS Business Management', 'college_id' => $colleges['CEMDS']],
            ['code' => 'BSDM', 'name' => 'BS Development Management', 'college_id' => $colleges['CEMDS']],
            ['code' => 'BSECON', 'name' => 'BS Economics', 'college_id' => $colleges['CEMDS']],
            ['code' => 'BSINTS', 'name' => 'BS International Studies', 'college_id' => $colleges['CEMDS']],
            ['code' => 'BSOA', 'name' => 'BS Office Administration', 'college_id' => $colleges['CEMDS']],

            // CED
            ['code' => 'BECE', 'name' => 'Bachelor of Early Childhood', 'college_id' => $colleges['CED']],
            ['code' => 'BEED', 'name' => 'Bachelor of Elementary Education', 'college_id' => $colleges['CED']],
            ['code' => 'BSED', 'name' => 'Bachelor of Secondary Education', 'college_id' => $colleges['CED']],
            ['code' => 'BSNE', 'name' => 'Bachelor of Special Needs Education', 'college_id' => $colleges['CED']],
            ['code' => 'BTLE', 'name' => 'Bachelor of Technology and Livelihood Education', 'college_id' => $colleges['CED']],
            ['code' => 'BSHM', 'name' => 'BS Hospitality Management', 'college_id' => $colleges['CED']],
            ['code' => 'BSTM', 'name' => 'BS Tourism Management', 'college_id' => $colleges['CED']],
            ['code' => 'TCP', 'name' => 'Teacher Certificate Program', 'college_id' => $colleges['CED']],
            ['code' => 'SHS', 'name' => 'Science High School', 'college_id' => $colleges['CED']],
            ['code' => 'ELED', 'name' => 'Elementary Education', 'college_id' => $colleges['CED']],
            ['code' => 'PELED', 'name' => 'Pre-Elementary Education', 'college_id' => $colleges['CED']],

            // CEIT
            ['code' => 'BSAE', 'name' => 'BS Agricultural and Biosystems Engineering', 'college_id' => $colleges['CEIT']],
            ['code' => 'BSARCH', 'name' => 'BS Architecture', 'college_id' => $colleges['CEIT']],
            ['code' => 'BSCE', 'name' => 'BS Civil Engineering', 'college_id' => $colleges['CEIT']],
            ['code' => 'BSCpE', 'name' => 'BS Computer Engineering', 'college_id' => $colleges['CEIT']],
            ['code' => 'BSCS', 'name' => 'BS Computer Science', 'college_id' => $colleges['CEIT']],
            ['code' => 'BSEE', 'name' => 'BS Electrical Engineering', 'college_id' => $colleges['CEIT']],
            ['code' => 'BSECE', 'name' => 'BS Electronics Engineering', 'college_id' => $colleges['CEIT']],
            ['code' => 'BSIE', 'name' => 'BS Industrial Engineering', 'college_id' => $colleges['CEIT']],
            ['code' => 'BSITEC', 'name' => 'BS Industrial Technology', 'college_id' => $colleges['CEIT']],
            ['code' => 'BSIT', 'name' => 'BS Information Technology', 'college_id' => $colleges['CEIT']],

            // CON
            ['code' => 'BSMT', 'name' => 'BS Medical Technology', 'college_id' => $colleges['CON']],
            ['code' => 'BSMID', 'name' => 'BS Midwifery', 'college_id' => $colleges['CON']],
            ['code' => 'BSN', 'name' => 'BS Nursing', 'college_id' => $colleges['CON']],
            ['code' => 'DMID', 'name' => 'Diploma in Midwifery', 'college_id' => $colleges['CON']],

            // CSPEAR
            ['code' => 'BPE', 'name' => 'Bachelor of Physical Education', 'college_id' => $colleges['CSPEAR']],
            ['code' => 'BSESS', 'name' => 'Bachelor of Exercise and Sports Sciences', 'college_id' => $colleges['CSPEAR']],

            // CVMBS
            ['code' => 'DVM', 'name' => 'Doctor of Veterinary Medicine', 'college_id' => $colleges['CVMBS']],

            // GSOLC
            ['code' => 'PHDA', 'name' => 'PhD in Agriculture', 'college_id' => $colleges['GSOLC']],
            ['code' => 'PHDE', 'name' => 'PhD in Education', 'college_id' => $colleges['GSOLC']],
            ['code' => 'PHDM', 'name' => 'PhD in Management', 'college_id' => $colleges['GSOLC']],
            ['code' => 'MBA', 'name' => 'Master in Business Administration', 'college_id' => $colleges['GSOLC']],
            ['code' => 'MAGRI', 'name' => 'Master in Agriculture', 'college_id' => $colleges['GSOLC']],
            ['code' => 'MAED', 'name' => 'Master of Arts in Education', 'college_id' => $colleges['GSOLC']],
            ['code' => 'MENG', 'name' => 'Master of Engineering', 'college_id' => $colleges['GSOLC']],
            ['code' => 'MMG', 'name' => 'Master of Management', 'college_id' => $colleges['GSOLC']],
            ['code' => 'MPS', 'name' => 'Master of Professional Studies', 'college_id' => $colleges['GSOLC']],
            ['code' => 'MSA', 'name' => 'MS Agriculture', 'college_id' => $colleges['GSOLC']],
            ['code' => 'MSB', 'name' => 'MS Biology', 'college_id' => $colleges['GSOLC']],
            ['code' => 'MSFS', 'name' => 'MS Food Science', 'college_id' => $colleges['GSOLC']],
            ['code' => 'MIT', 'name' => 'Master in Information Technology', 'college_id' => $colleges['GSOLC']],
        ];

        foreach ($programs as $program) {
            DB::table('programs')->updateOrInsert(
                ['code' => $program['code']],
                [
                    'name' => $program['name'],
                    'college_id' => $program['college_id'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
