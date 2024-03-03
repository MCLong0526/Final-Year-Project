<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssignBuyerRoleToUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $buyerRole = Role::where('name', 'buyer')->first();
        $buyerRoleId = $buyerRole->id;

        User::all()->each(function ($user) use ($buyerRoleId) {
            $user->roles()->syncWithoutDetaching([$buyerRoleId]);
        });
    }
}
