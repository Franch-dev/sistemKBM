<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SekretarisSeeder extends Seeder
{
    public function run(): void
    {
        $kelasList = DB::table('kelas')->get();

        foreach ($kelasList as $kelas) {
            $cleanName = Str::slug($kelas->nama_kelas, '');
            $email = "sekre.{$cleanName}@sekolah.sch.id";

            DB::table('users')->insert([
                'name'       => "Sekretaris {$kelas->nama_kelas}",
                'email'      => strtolower($email),
                'password'   => Hash::make('password123'),
                'role_id'    => 2,
                'kelas_id'   => $kelas->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
