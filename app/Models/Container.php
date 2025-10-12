<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'container_number',
        'branch_id',
        'status',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function containerOrders()
    {
        return $this->hasMany(ContainerOrder::class);
    }
}
