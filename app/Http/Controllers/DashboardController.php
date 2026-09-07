<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\Kelas;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Dashboard admin: menampilkan seluruh data jurusan & kelas.
     */
    public function admin()
    {
        $jurusans = Jurusan::withCount('kelas')->orderBy('nama_jurusan')->get();
        $kelas = Kelas::with('jurusan')->orderBy('nama_kelas')->get();

        return view('admin.dashboard', [
            'user'         => Auth::user(),
            'jurusans'     => $jurusans,
            'kelas'        => $kelas,
            'totalJurusan' => $jurusans->count(),
            'totalKelas'   => $kelas->count(),
        ]);
    }

    /**
     * Dashboard sekretaris: menampilkan kelas & jurusan miliknya sendiri.
     */
    public function sekretaris()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Jaga-jaga kalau route ini kepanggil tanpa user login
        if (! $user) {
            return redirect()->route('login');
        }

        // loadMissing lebih aman daripada load() kalau kelas_id null
        $user->loadMissing('kelas.jurusan');

        return view('sekretaris.dashboard', [
            'user'  => $user,
            'kelas' => $user->kelas, // bisa null kalau user belum diassign ke kelas manapun
        ]);
    }
}