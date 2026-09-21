<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * 4 role sesuai tabel fitur. Aman dijalankan ulang (id 2 yang dulu
     * bernama "sekretaris" otomatis diganti menjadi "class_secretary").
     */
    public function run(): void
    {
        $roles = [
            1 => 'admin',
            2 => 'class_secretary',
            3 => 'staff_secretary',
            4 => 'staff',
        ];

        foreach ($roles as $id => $name) {
            DB::table('roles')->updateOrInsert(
                ['id' => $id],
                ['role_name' => $name, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
