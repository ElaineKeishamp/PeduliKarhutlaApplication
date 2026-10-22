<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@pedulikarhutla.test'],
            [
                'name' => 'Admin Peduli Karhutla',
                'password' => 'password',
                'role' => 'admin',
            ],
        );
    }
}
