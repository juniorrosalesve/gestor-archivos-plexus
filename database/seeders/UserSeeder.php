<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Junior Rosales',
            'email' => 'junior@admin.com',
            'password' => bcrypt('12'),
            'access' => 'a'
        ]);
        User::create([
            'name' => 'David Molina',
            'email' => 'dmolinap83@gmail.com',
            'password' => bcrypt('1324'),
            'access' => 'a'
        ]);
        // User::create([
        //     'name' => 'Arturo Kimura',
        //     'email' => 'arturo@admin.com',
        //     'password' => bcrypt('12'),
        //     'access' => 'd'
        // ]);
        // User::create([
        //     'name' => 'Christian Espinosa',
        //     'email' => 'christian@admin.com',
        //     'password' => bcrypt('12'),
        //     'access' => 'd'
        // ]);
        // User::create([
        //     'name' => 'Manuel Meza',
        //     'email' => 'manuel@admin.com',
        //     'password' => bcrypt('12'),
        //     'access' => 'd'
        // ]);
        // User::create([
        //     'name' => 'Antelmo',
        //     'email' => 'antelmo@admin.com',
        //     'password' => bcrypt('12'),
        //     'access' => 'd'
        // ]);
    }
}
