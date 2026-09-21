<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    /** Akun contoh untuk role Staff Secretary (3) dan Staff (4). */
    public function run(): void
    {
        $accounts = [
            ['name' => 'Staff Secretary', 'email' => 'staffsecretary@sekolah.sch.id', 'role_id' => 3],
            ['name' => 'Staff',           'email' => 'staff@sekolah.sch.id',          'role_id' => 4],
        ];

        foreach ($accounts as $a) {
            DB::table('users')->updateOrInsert(
                ['email' => $a['email']],
                [
                    'name'       => $a['name'],
                    'password'   => Hash::make('password123'),
                    'role_id'    => $a['role_id'],
                    'kelas_id'   => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
