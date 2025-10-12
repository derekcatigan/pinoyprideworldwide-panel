<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContainerOrder extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'container_id',
        'order_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function container()
    {
        return $this->belongsTo(Container::class);
    }
}
