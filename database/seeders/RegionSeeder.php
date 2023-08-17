<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Region;
use App\Models\Country;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Region::create([
            'name' => 'Región 1',
            'userId' => 2
        ]);
        Country::create([
            'name' => 'México',
            'regionId' => 1
        ]);
        Country::create([
            'name' => 'Venezuela',
            'regionId' => 1
        ]);
    }
}
