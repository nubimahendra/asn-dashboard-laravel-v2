<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'modules',
        'nip',
        'pd_scope',
    ];

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
            'modules' => 'array',
        ];
    }

    /**
     * Check if user has access to a specific module.
     */
    public function hasModuleAccess(string $module): bool
    {
        if ($this->role === 'admin') {
            return true;
        }

        return in_array($module, $this->modules ?? ['mari']);
    }

    /**
     * Check if user is scoped to a specific PD (Perangkat Daerah).
     */
    public function hasPdScope(): bool
    {
        return !empty($this->pd_scope);
    }

    /**
     * Get the list of unor_id (from ref_unor) that fall within this user's PD scope.
     * Returns null if user has no PD scope (Super Admin).
     */
    public function getScopedUnorIds(): ?array
    {
        if (!$this->hasPdScope()) {
            return null;
        }

        return \App\Models\RefUnor::where('nama', $this->pd_scope)
            ->pluck('id')
            ->toArray();
    }
    /**
     * Get the chat messages for the user.
     */
    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * Get the pegawai data associated with the user's NIP.
     */
    public function snapshotPegawai()
    {
        return $this->hasOne(SnapshotPegawai::class, 'nip_baru', 'nip');
    }
}
