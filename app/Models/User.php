<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_STAFF_SECRETARY = 'staff_secretary';
    public const ROLE_STAFF = 'staff';
    public const ROLE_CLASS_SECRETARY = 'class_secretary';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'kelas_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function roleName(): ?string
    {
        return $this->role?->role_name;
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->roleName(), $roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    public static function roleLabel(?string $roleName): string
    {
        return match ($roleName) {
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_STAFF_SECRETARY => 'Staff Secretary',
            self::ROLE_STAFF => 'Staff',
            self::ROLE_CLASS_SECRETARY => 'Class Secretary',
            default => '-',
        };
    }

    /** URL dashboard milik masing-masing role. */
    public function dashboardPath(): string
    {
        return match ($this->roleName()) {
            self::ROLE_ADMIN => '/admin/dashboard',
            self::ROLE_STAFF_SECRETARY => '/staff-secretary/dashboard',
            self::ROLE_STAFF => '/staff/dashboard',
            self::ROLE_CLASS_SECRETARY => '/class-secretary/dashboard',
            default => '/login',
        };
    }

    /** Bentuk data user yang dikirim ke API (tanpa password). */
    public function toApiArray(): array
    {
        $this->loadMissing('role', 'kelas.jurusan');

        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'role_id'    => $this->role_id,
            'role'       => $this->roleName(),
            'role_label' => self::roleLabel($this->roleName()),
            'kelas_id'   => $this->kelas_id,
            'kelas'      => $this->kelas ? [
                'id'         => $this->kelas->id,
                'nama_kelas' => $this->kelas->nama_kelas,
                'jurusan'    => $this->kelas->jurusan?->nama_jurusan,
            ] : null,
            'dashboard'  => $this->dashboardPath(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
