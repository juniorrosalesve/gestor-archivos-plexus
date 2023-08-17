<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries  =   [
            'México',
            'Chile',
            'Puerto Rico',
            'Perú',
            'Paraguay',
            'Bolivia',
            'Colombia',
            'Ecuador',
            'República Dominicana',
            'Panamá',
            'Guatemala',
            'El Salvador',
            'Honduras'
        ];
        for($i = 0; $i < sizeof($countries); $i++)
            Country::create(['name' => $countries[$i]]);
    }
}
