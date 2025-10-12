<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Box extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'location_id',
        'branch_id',
        'box_type_id',
    ];

    public function boxType()
    {
        return $this->belongsTo(BoxType::class, 'box_type_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function destination()
    {
        return $this->belongsTo(BoxLocation::class, 'location_id');
    }
}
