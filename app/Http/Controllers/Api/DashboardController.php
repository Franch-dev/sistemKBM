<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * GET /api/dashboard — isi dashboard menyesuaikan role user yang login.
     */
    public function show(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $role = $user->roleName();

        $payload = [
            'role' => $role,
            'user' => $user->toApiArray(),
        ];

        switch ($role) {
            case User::ROLE_ADMIN:
                $payload['stats'] = [
                    'total_users'   => User::count(),
                    'total_jurusan' => Jurusan::count(),
                    'total_kelas'   => Kelas::count(),
                ];
                $payload['users_per_role'] = Role::withCount('users')->orderBy('id')->get()
                    ->map(fn (Role $r) => [
                        'role'  => $r->role_name,
                        'label' => User::roleLabel($r->role_name),
                        'total' => $r->users_count,
                    ])->values();
                $payload['recent_users'] = User::with('role', 'kelas.jurusan')
                    ->latest()->limit(5)->get()
                    ->map(fn (User $u) => $u->toApiArray())->values();
                break;

            case User::ROLE_CLASS_SECRETARY:
                $kelas = $user->kelas?->load('jurusan');
                $payload['kelas'] = $kelas ? [
                    'id'         => $kelas->id,
                    'nama_kelas' => $kelas->nama_kelas,
                    'tingkat'    => $kelas->tingkat,
                    'kelompok'   => $kelas->kelompok,
                    'jurusan'    => $kelas->jurusan?->nama_jurusan,
                ] : null;
                break;

            default: // staff_secretary & staff
                $payload['stats'] = [
                    'total_jurusan' => Jurusan::count(),
                    'total_kelas'   => Kelas::count(),
                ];
                $payload['jurusans'] = Jurusan::withCount('kelas')->orderBy('nama_jurusan')->get()
                    ->map(fn (Jurusan $j) => [
                        'kode'        => $j->kode_jurusan,
                        'nama'        => $j->nama_jurusan,
                        'jumlah_kelas' => $j->kelas_count,
                    ])->values();
        }

        return response()->json($payload);
    }
}
