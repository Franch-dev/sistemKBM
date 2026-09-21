<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Mengelola Akun User (khusus Admin).
 * GET    /api/users/options   -> daftar role & kelas untuk form
 * GET    /api/users           -> daftar user (?q=cari&role_id=1&page=1)
 * POST   /api/users           -> buat akun
 * GET    /api/users/{id}      -> detail
 * PUT    /api/users/{id}      -> ubah akun
 * DELETE /api/users/{id}      -> hapus akun
 */
class UserController extends Controller
{
    public function options(): JsonResponse
    {
        return response()->json([
            'roles' => Role::orderBy('id')->get()->map(fn (Role $r) => [
                'id'        => $r->id,
                'role_name' => $r->role_name,
                'label'     => User::roleLabel($r->role_name),
            ]),
            'kelas' => Kelas::orderBy('nama_kelas')->get(['id', 'nama_kelas']),
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $query = User::with('role', 'kelas.jurusan')->orderBy('name');

        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $query->where(function ($w) use ($search) {
                $w->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->query('role_id'));
        }

        $users = $query->paginate(10)->through(fn (User $u) => $u->toApiArray());

        return response()->json($users);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateData($request);

        $user = User::create($data);

        return response()->json([
            'message' => 'Akun berhasil dibuat.',
            'data'    => $user->load('role', 'kelas.jurusan')->toApiArray(),
        ], 201);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json(['data' => $user->toApiArray()]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $data = $this->validateData($request, $user);

        // Admin tidak boleh mengubah role akunnya sendiri (supaya tidak terkunci).
        if ($request->user()->is($user) && (int) $data['role_id'] !== (int) $user->role_id) {
            return response()->json([
                'message' => 'Anda tidak dapat mengubah role akun Anda sendiri.',
                'errors'  => ['role_id' => ['Anda tidak dapat mengubah role akun Anda sendiri.']],
            ], 422);
        }

        $user->update($data);

        // Password diganti oleh admin -> paksa user tersebut login ulang.
        if (isset($data['password']) && ! $request->user()->is($user)) {
            $user->tokens()->delete();
        }

        return response()->json([
            'message' => 'Akun berhasil diperbarui.',
            'data'    => $user->refresh()->load('role', 'kelas.jurusan')->toApiArray(),
        ]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        if ($request->user()->is($user)) {
            return response()->json(['message' => 'Anda tidak dapat menghapus akun Anda sendiri.'], 422);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json(['message' => 'Akun berhasil dihapus.']);
    }

    private function validateData(Request $request, ?User $user = null): array
    {
        $classSecretaryRoleId = (int) Role::where('role_name', User::ROLE_CLASS_SECRETARY)->value('id');

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8'],
            'role_id'  => ['required', 'integer', 'exists:roles,id'],
            'kelas_id' => [
                Rule::requiredIf((int) $request->input('role_id') === $classSecretaryRoleId),
                'nullable', 'integer', 'exists:kelas,id',
            ],
        ], [
            'kelas_id.required' => 'Kelas wajib dipilih untuk role Class Secretary.',
            'email.unique'      => 'Email sudah dipakai akun lain.',
            'password.min'      => 'Password minimal 8 karakter.',
        ]);

        // Kelas hanya relevan untuk Class Secretary.
        if ((int) $data['role_id'] !== $classSecretaryRoleId) {
            $data['kelas_id'] = null;
        }

        // Saat edit, password kosong = tidak diganti.
        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $data;
    }
}
