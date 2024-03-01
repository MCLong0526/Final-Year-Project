<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'user_id' => 1,
            'phone_number' => '0162770526',
            'username' => 'MC0526',
            'email' => '75613@siswa.unimas.my',
            'password' => bcrypt('Michael0526'),
        ]);
    }
}
