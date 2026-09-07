<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
            JurusanDanKelasSeeder::class, // Run pertama: buat 8 jurusan & 48 kelas
            SekretarisSeeder::class,     // Run kedua: buat 48 akun sekretaris
        ]);
    }
}