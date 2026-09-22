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
        'cluster_name',
        'google_id',
        'avatar',
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
        ];
    }

    /**
     * Check if user has Admin / Full Access role (USER / ADMIN)
     */
    public function isAdmin(): bool
    {
        $r = strtolower($this->role ?? 'user');
        return in_array($r, ['admin', 'user']);
    }

    /**
     * Check if user is a Visitor (Read-Only)
     */
    public function isVisitor(): bool
    {
        return strtolower($this->role ?? '') === 'visitor';
    }

    /**
     * Check if user is locked to a specific cluster
     */
    public function hasClusterLock(): bool
    {
        return !empty($this->cluster_name);
    }

    /**
     * Get formatted display role
     */
    public function getRoleBadgeLabelAttribute(): string
    {
        if ($this->isVisitor()) {
            return $this->hasClusterLock() ? 'VISITOR (' . strtoupper($this->cluster_name) . ')' : 'VISITOR';
        }
        return 'USER (ADMIN)';
    }
}
