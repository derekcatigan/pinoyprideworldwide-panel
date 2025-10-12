<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enum\UserRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, HasApiTokens;

    protected $fillable = [
        'branch_id',
        'user_code',
        'email',
        'contact',
        'role',
        'status',
        'password',
    ];
    protected $appends = ['role_name'];

    public function getRoleNameAttribute(): string
    {
        return $this->role?->displayName() ?? '';
    }

    public static function generateUserCode(string $role): string
    {
        $prefix = match (strtolower($role)) {
            'admin' => 'ADM',
            'branch_admin' => 'BCH',
            'agent' => 'AGT',
            default => 'USR',
        };

        $latestCode = static::query()
            ->where('user_code', 'like', "$prefix-%")
            ->orderBy('user_code', 'desc')
            ->value('user_code');

        $nextNumber = 1001;
        if ($latestCode) {
            $lastNumber = (int) str_replace("$prefix-", '', $latestCode);
            $nextNumber = $lastNumber + 1;
        }

        return sprintf('%s-%04d', $prefix, $nextNumber);
    }

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
