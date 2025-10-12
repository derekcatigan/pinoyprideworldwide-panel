<?php

namespace Database\Seeders;

use App\Enum\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'email' => "admin@email.com",
            'contact' => "09000000001",
            'branch_id' => null,
            'user_code' => "ADM-1001",
            'role' => UserRole::Admin,
            'status' => "active",
            'password' => Hash::make('password'),
        ]);

        $adminProfile = $admin->profile()->create([
            'first_name' => "Admin",
            'last_name' => "User",
            'gender' => "male",
            "birthdate" => "2003-06-15",
        ]);

        $adminProfile->address()->create([
            'street' => "",
            'barangay' => "Barangay 1",
            'province' => "Province 1",
            'city' => "City 1",
            'country' => "Philippines",
        ]);
    }
}
