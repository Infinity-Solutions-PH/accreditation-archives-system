<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CollegeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colleges = [
            [
                'code' => 'CAS',
                'name' => 'College of Arts and Sciences',
            ],
            [
                'code' => 'CCJ',
                'name' => 'College of Criminal Justice',
            ],
            [
                'code' => 'CEIT',
                'name' => 'College of Engineering and Information Technology',
            ],
            [
                'code' => 'CED',
                'name' => 'College of Education',
            ],
            [
                'code' => 'CEMDS',
                'name' => 'College of Economics, Management and Development Studies',
            ],
            [
                'code' => 'CAFENR',
                'name' => 'College of Agriculture, Food, Environment and Natural Resources',
            ],
            [
                'code' => 'CON',
                'name' => 'College of Nursing',
            ],
            [
                'code' => 'CSPEAR',
                'name' => 'College of Sports, Physical Education and Recreation',
            ],
            [
                'code' => 'CVMBS',
                'name' => 'College of Veterinary Medicine and Biomedical Sciences',
            ],
            [
                'code' => 'GSOLC',
                'name' => 'Graduate School and Open Learning College',
            ],
        ];

        foreach ($colleges as $college) {
            DB::table('colleges')->updateOrInsert(
                ['code' => $college['code']],
                ['name' => $college['name'], 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
