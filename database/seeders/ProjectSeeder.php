<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Project;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Project::create([
            'name' => 'Proyecto Uno',
            'regionId' => 1,
            'countryId' => 1,
            'managerId' => 3,
            'delivery' => '2023-08-30'
        ]);
        Project::create([
            'name' => 'Proyecto Dos',
            'regionId' => 1,
            'countryId' => 1,
            'managerId' => 3,
            'delivery' => '2023-08-27'
        ]);
        Project::create([
            'name' => 'Proyecto Tres',
            'regionId' => 1,
            'countryId' => 1,
            'managerId' => 3,
            'delivery' => '2023-08-25'
        ]);
    }
}
