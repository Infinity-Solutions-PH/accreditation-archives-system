<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [
            [
                'code' => 'AREA I',
                'name' => 'Vision, Mission, Goals and Objectives',
                'slug' => 'area-i',
                'description' => 'Clarity, alignment, dissemination, and relevance of VMGO to stakeholders.',
                'order_no' => 1
            ],
            [
                'code' => 'AREA II',
                'name' => 'Faculty',
                'slug' => 'area-ii',
                'description' => 'Faculty qualifications, development, performance, and scholarly productivity.',
                'order_no' => 2
            ],
            [
                'code' => 'AREA III',
                'name' => 'Curriculum and Instruction',
                'slug' => 'area-iii',
                'description' => 'Curriculum design, delivery, review, and learning effectiveness.',
                'order_no' => 3
            ],
            [
                'code' => 'AREA IV',
                'name' => 'Support to Students',
                'slug' => 'area-iv',
                'description' => 'Student services including guidance, health, admission, and career support.',
                'order_no' => 4
            ],
            [
                'code' => 'AREA V',
                'name' => 'Research',
                'slug' => 'area-v',
                'description' => 'Research programs, outputs, dissemination, and utilization.',
                'order_no' => 5
            ],
            [
                'code' => 'AREA VI',
                'name' => 'Extension and Community Involvement',
                'slug' => 'area-vi',
                'description' => 'Community engagement, outreach programs, and societal impact.',
                'order_no' => 6
            ],
            [
                'code' => 'AREA VII',
                'name' => 'Library',
                'slug' => 'area-vii',
                'description' => 'Library resources, services, digital access, and learning support.',
                'order_no' => 7
            ],
            [
                'code' => 'AREA VIII',
                'slug' => 'area-viii',
                'name' => 'Physical Plant and Facilities',
                'description' => 'Campus infrastructure, classrooms, safety, and facility maintenance.',
                'order_no' => 8
            ],
            [
                'code' => 'AREA IX',
                'name' => 'Laboratories',
                'slug' => 'area-ix',
                'description' => 'Laboratory facilities, equipment, safety, and instructional support.',
                'order_no' => 9
            ],
            [
                'code' => 'AREA X',
                'slug' => 'area-x',
                'name' => 'Administration',
                'description' => 'Governance, leadership, planning, policies, and quality management systems.',
                'order_no' => 10
            ],
        ];

        foreach ($areas as $area) {
            Area::updateOrCreate(
                ['code' => $area['code']],
                $area
            );
        }
    }
}
