<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create the user
        $user = User::create([
            'phone_number' => '0162770526',
            'username' => 'MC0526',
            'email' => '75613@siswa.unimas.my',
            'password' => bcrypt('Michael0526'),
        ]);

        // Find the roles with role_id = 1, 2, and 3
        $roles = Role::whereIn('role_id', [1, 2, 3])->get();

        // Check if the roles are found
        if ($roles->count() > 0) {
            // Attach the roles to the user
            $user->roles()->attach($roles);
        } else {
            // Handle the case where the roles are not found
            $this->command->info('Roles with role_id 1, 2, or 3 not found');
        }
    }
}
