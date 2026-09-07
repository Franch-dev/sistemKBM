<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            'name'       => 'Super Admin',
            'email'      => 'admin@sekolah.sch.id',
            'password'   => Hash::make('admin123'),
            'role_id'    => 1,
            'kelas_id'   => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
