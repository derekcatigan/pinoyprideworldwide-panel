<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'branch_code',
        'branch_name',
        'branch_owner',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
