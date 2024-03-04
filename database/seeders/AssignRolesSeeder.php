<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssignRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Find the user with the username "MC0526"
        $user = User::where('username', 'MC0526')->first();

        // Find roles with role_id 1 and 3
        $roles = Role::whereIn('role_id', [1, 3])->get();

        // Attach roles to the user
        $user->roles()->attach($roles);
    }
}
