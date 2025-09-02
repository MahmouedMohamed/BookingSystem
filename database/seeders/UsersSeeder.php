<?php

namespace Database\Seeders;

use App\Modules\Users\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['admin', 'provider', 'customer'];
        foreach ($roles as $key => $role) {
            User::factory()->create([
            'name' => Str::ucfirst($role). ' User',
            'email' => $role.'@booking-system.com',
            'password' => Hash::make('12345678'),
            'role' => $role
        ]);
        }

    }
}
