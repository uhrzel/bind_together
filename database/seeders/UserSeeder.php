<?php

namespace Database\Seeders;

use App\Enums\UserTypeEnum;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Map roles to their corresponding email addresses
        $roleEmails = [
            UserTypeEnum::SUPERADMIN => 'superadmin@gmail.com',
            UserTypeEnum::ADMINSPORT => 'adminsport@gmail.com',
            UserTypeEnum::ADMINORG => 'adminorg@gmail.com',
            UserTypeEnum::COACH => 'coach@gmail.com',
            UserTypeEnum::ADVISER => 'adviser@gmail.com',
            UserTypeEnum::STUDENT => 'student@gmail.com',
        ];

        // Loop through each role and create a user
        foreach ($roleEmails as $roleName => $email) {
            // Create the user with a specific email and default password
            $user = User::factory()->create([
                'email' => $email,
                'password' => Hash::make('password'), // Set password to 'password'
            ]);

            // Assign the role to the user
            $user->assignRole($roleName);
        }
    }
}
