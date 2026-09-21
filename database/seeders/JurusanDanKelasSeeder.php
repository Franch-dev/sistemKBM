<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JurusanDanKelasSeeder extends Seeder
{
    public function run(): void
    {
        $jurusanList = [
            ['kode' => 'TKJ', 'nama' => 'Teknik Komputer dan Jaringan'],
            ['kode' => 'DKV', 'nama' => 'Desain Komunikasi Visual'],
            ['kode' => 'RPL', 'nama' => 'Rekayasa Perangkat Lunak'],
            ['kode' => 'AK', 'nama' => 'Akuntansi dan Keuangan'],
            ['kode' => 'BB',  'nama' => 'Busana Butik'],
            ['kode' => 'TKR', 'nama' => 'Teknik Kendaraan Ringan'],
            ['kode' => 'TP', 'nama' => 'Teknik Permesinan'],
            ['kode' => 'TPL', 'nama' => 'Teknik Pengelasan'],
        ];

        $tingkatList = ['10', '11', '12'];
        $pararelList = ['A', 'B'];

        foreach ($jurusanList as $j) {
            // Insert atau Dapatkan ID Jurusan
            $jurusanId = DB::table('jurusans')->insertGetId([
                'kode_jurusan' => $j['kode'],
                'nama_jurusan' => $j['nama'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Generate 6 Kelas per Jurusan (10A, 10B, 11A, 11B, 12A, 12B)
            foreach ($tingkatList as $tingkat) {
                foreach ($pararelList as $pararel) {
                    DB::table('kelas')->insert([
                        'jurusan_id' => $jurusanId,
                        'nama_kelas' => "{$tingkat} {$j['kode']} {$pararel}", // Contoh: 10 RPL A
                        'tingkat'    => $tingkat,
                        'kelompok'   => $pararel,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}