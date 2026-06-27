<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
    ];

    public const ROLE_SUPERADMIN = 'superadmin';
    public const ROLE_COMPTABILITE = 'comptabilite';
    public const ROLE_ACCUEIL = 'accueil';

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPERADMIN;
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            self::ROLE_SUPERADMIN => 'Super Administrateur',
            self::ROLE_COMPTABILITE => 'Comptabilité',
            self::ROLE_ACCUEIL => 'Accueil',
            default => ucfirst((string) $this->role),
        };
    }

    /**
     * Modules accessibles selon le rôle (le superadmin a tout).
     */
    public function modules(): array
    {
        $modules = config('admin.modules', []);

        if ($this->isSuperAdmin()) {
            return $modules;
        }

        return array_filter(
            $modules,
            fn (array $module) => in_array($this->role, $module['roles'], true)
        );
    }

    public function canAccessModule(string $key): bool
    {
        return array_key_exists($key, $this->modules());
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
