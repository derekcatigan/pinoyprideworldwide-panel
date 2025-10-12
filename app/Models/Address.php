<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'profile_id',
        'street',
        'barangay',
        'city',
        'province',
        'country',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
