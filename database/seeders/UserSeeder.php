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
            'name' => 'Domingo Conrrado',
            'email' => 'dominic@admin.com',
            'password' => bcrypt('12'),
            'access' => 'd'
        ]);
        User::create([
            'name' => 'Juan Gomez',
            'email' => 'juan@admin.com',
            'password' => bcrypt('12'),
            'access' => 'g'
        ]);
    }
}
